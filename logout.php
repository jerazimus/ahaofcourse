<?php
// logout.php

session_start(); // Start the session

// Unset all session variables
$_SESSION = [];

// If there's a session cookie, delete it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to the home page or login page
header("Location: /"); // Change this to your desired redirect URL
exit;

