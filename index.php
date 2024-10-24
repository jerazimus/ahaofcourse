<?php

require_once 'vendor/autoload.php';
require_once 'db.php'; 

// Load configuration
$config = parse_ini_file(__DIR__ . "/config.ini", true);
session_start(); // Start the session

try {
    $pdo = new PDO('sqlite:' . $config["db"]["location"]); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//echo "Requested URI: " . htmlspecialchars($uri) . "<br>";

$protectedRoutes = ['/dashboard', '/courses', '/lessons']; // Add all protected routes here

// Check for testing mode and set session values
if ($config['env']['testing'] === '1') { // Compare to string "1"
    // Set test user session if not already set
    if (!isset($_SESSION['test_user'])) {
        $_SESSION['test_user'] = [
            'email' => 'jeremy@test.com',
            'name' => 'Jeremy Test',
        ];
        echo "Test session set: ";
        var_dump($_SESSION); // Output session data for debugging
    }

    // Only redirect if we're not already on the dashboard
    if ($uri !== '/dashboard') {
        echo "Redirecting to dashboard...";
        header('Location: /dashboard'); // Redirect to the dashboard
        exit; // Ensure no further execution
    }
} else {
    //destroy test user
    unset($_SESSION['test_user']);
    session_destroy();
}

// Google Client Setup for production environment
$client = new Google_Client();
$client->setClientId($config['gauth']['google_client_id']);
$client->setClientSecret($config['gauth']['google_client_secret']);
$client->setRedirectUri($config['gauth']['google_redirect_uri']);
$client->addScope('email');
$client->addScope('profile');

// Authentication Logic
$loginUrl = null; // Initialize loginUrl variable

if (!isset($_SESSION['user'])) {
    // User not authenticated
    if ($config['env']['testing'] !== '1') {
        $loginUrl = $client->createAuthUrl();
    } else {
        logUserInDB($pdo, null, $_SESSION['test_user']['email'], $_SESSION['test_user']['name']);
        $_SESSION['user'] = [
            'email' => $_SESSION['test_user']['email'],
            'name' => $_SESSION['test_user']['name'],
            'google_id' => null // Set google_id to null for test users
        ];
    }
} else {
    // If the user is authenticated via Google, retrieve their information
    // Here, you should check if the user has a Google ID
    if (isset($_GET['code'])) { // Check if Google returned an authentication code
        // Exchange the code for an access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token['access_token']);

        // Get user profile data
        $googleOAuth = new Google_Service_Oauth2($client);
        $googleUser = $googleOAuth->userinfo->get();

        // Set user information in the session
        $_SESSION['user'] = [
            'email' => $googleUser->email,
            'name' => $googleUser->name,
            'google_id' => $googleUser->id // Store the Google ID
        ];

        // Log the user into the database
        logUserInDB($pdo, $googleUser->id, $googleUser->email, $googleUser->name);
    }
}


// Routing logic
switch ($uri) {
    case '/':
        include 'views/main.php'; // Main page view
        break;
    case '/dashboard':
        showDashboard($pdo, $loginUrl ?? null); //  null coalescing op checks if not nnull
        break;
    case '/logout':
        include 'logout.php'; // Include the logout script
        break;
    default:
        http_response_code(404);
        echo '404 Not Found'; // Handle 404 errors
        break;
}

function showDashboard($pdo, $loginUrl) {
    if ($loginUrl === null) {
        echo "TESTING MODE";
        echo "User email: " . htmlspecialchars($_SESSION['test_user']['email']); // Display user info
    } else {
        echo "Please <a href=\"$loginUrl\">login</a> to access the dashboard.";
    }

    include 'views/users/dashboard.php'; // Include the actual dashboard view
}
