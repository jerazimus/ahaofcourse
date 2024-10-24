<?php
session_start(); // Start the session

// Clear the session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the home page or login page
header("Location: /"); // Change this to your desired redirect URL
exit;