<?php

require_once 'vendor/autoload.php';

$config = parse_ini_file(__DIR__ . "/config.ini", true);

session_start();

$client = new Google_Client();
$client->setClientId($config['gauth']['google_client_id']);
$client->setClientSecret($config['gauth']['google_client_secret']);
$client->setRedirectUri($config['gauth']['google_redirect_uri']);

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
    header('Location: views/users/dashboard.php');
    exit;
} else {
    // Handle the error if the user denied access or there was an issue
    echo 'Google authentication failed.';
}


