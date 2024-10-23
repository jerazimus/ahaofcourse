<?php

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

$client = new Google_Client();
$client->setClientId(getenv('GOOGLE_CLIENT_ID')); // Use environment variable for Client ID
$client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET')); // Use environment variable for Client Secret
$client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI')); // Use your actual domain or test environment

// Check if redirect URI is correctly set
if (!$client->getRedirectUri()) {
    die('Redirect URI is not set correctly.');
}


// Handle the OAuth response
if (isset($_GET['code'])) {
    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    // Set the access token
    $client->setAccessToken($token['access_token']);
    
    // Create an OAuth2 service to retrieve user information
    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();
    
    // Store user information in the session
    $_SESSION['user_email'] = $userInfo->email;
    $_SESSION['user_name'] = $userInfo->name;
    
    // Redirect the user to the dashboard or any other protected page
    header('Location: dashboard.php');
    exit;
} else {
    // Handle the error if the user denied access or there was an issue
    echo 'Google authentication failed.';
}


