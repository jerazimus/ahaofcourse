<?php

require_once 'vendor/autoload.php';
require_once 'db.php'; 

// Load configuration
$config = parse_ini_file(__DIR__ . "/config.ini", true);
session_start(); // Start the session

// Connect to the database
try {
    $pdo = new PDO('sqlite:' . $config["db"]["location"]); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check for testing mode and set session values
if ($config['env']['testing'] === '1') { 
    // Testing mode enabled
    if (!isset($_SESSION['test_user'])) {
        $_SESSION['test_user'] = [
            'email' => 'jeremy@test.com',
            'name' => 'Jeremy Test'
        ];
        $_SESSION['user'] = [
            'email' => $_SESSION['test_user']['email'],
            'name' => $_SESSION['test_user']['name'],
            'google_id' => null // No Google ID for test users
        ];

        // Log test user in the database
        logUserInDB($pdo, null, $_SESSION['test_user']['email'], $_SESSION['test_user']['name']);
    }

} else {
    // Production mode, clear test user
    unset($_SESSION['test_user']);

    // Google Client Setup
    $client = new Google_Client();
    $client->setClientId($config['gauth']['google_client_id']);
    $client->setClientSecret($config['gauth']['google_client_secret']);
    $client->setRedirectUri($config['gauth']['google_redirect_uri']);
    $client->addScope('email');
    $client->addScope('profile');

    // Authentication Logic
    if (!isset($_SESSION['user'])) {
        // User not authenticated, redirect to Google OAuth if not in testing mode
        if (isset($_GET['code'])) {
            // Google has returned an auth code, exchange for access token
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token['access_token']);

            // Get Google user profile
            $googleOAuth = new Google_Service_Oauth2($client);
            $googleUser = $googleOAuth->userinfo->get();

            // Set user information in the session
            $_SESSION['user'] = [
                'email' => $googleUser->email,
                'name' => $googleUser->name,
                'google_id' => $googleUser->id
            ];

            // Log the user into the database
            logUserInDB($pdo, $googleUser->id, $googleUser->email, $googleUser->name);

            // Redirect to the dashboard
            header('Location: /dashboard');
            exit();
        } else {
            // Not authenticated, redirect to Google login
            $loginUrl = $client->createAuthUrl();
        }
    }
}

// Routing logic
switch ($uri) {
    case '/':
        include 'views/main.php'; // Main page view
        break;

    case '/dashboard':
        showDashboard($pdo, $loginUrl ?? null); // If no login URL, user is logged in
        break;

    case '/logout':
        session_start();
        session_unset();
        session_destroy();
        header('Location: /');
        exit();

    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}

// Show the dashboard function
function showDashboard($pdo, $loginUrl) {
    if (isset($_SESSION['user'])) {
        echo "Welcome, " . htmlspecialchars($_SESSION['user']['name']) . "!";

        // Include the dashboard view
        include 'views/users/dashboard.php';
    } else {
        if ($loginUrl !== null) {
            // If no user is authenticated, show Google login link
            echo "Please <a href=\"$loginUrl\">login</a> to access the dashboard.";
        }
    }
}

// Log user in database function
function logUserInDB($pdo, $googleId, $email, $name) {
    $stmt = $pdo->prepare("INSERT INTO users (google_id, email, name, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now')) 
                           ON CONFLICT(email) DO UPDATE SET google_id = ?, updated_at = datetime('now')");
    $stmt->execute([$googleId, $email, $name, $googleId]);
}
