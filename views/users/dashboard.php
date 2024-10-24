<?php

// session_start();

   // Check if the user is logged in
   if (isset($_SESSION['user'])) {
    // Retrieve user information from session
    $userEmail = htmlspecialchars($_SESSION['user']['email']); // Sanitize output
    $userName = htmlspecialchars($_SESSION['user']['name']); // Sanitize output
    // echo "<h1>Welcome, $userName</h1>"; // Display the user's name
    // echo "<p>Your email: $userEmail</p>"; // Display the user's email
} else {
    // If no user is logged in, display a message or redirect
    $userEmail = '';
    echo "<p>Please log in to access your dashboard.</p>";
}
?>

<h1>Users#Dashboard</h1>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Welcome, <?= $userEmail ?></h1>
    <p><a href="/logout">Logout</a></p>

</body>
</html>