<?php

session_start();

if (isset($_SESSION['user_email']) && isset($_SESSION['user_name'])){
    $name = htmlspecialchars($_SESSION['user_name']);
    $email = htmlspecialchars($_SESSION['user_email']);
} else {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <nav>
        <h4>AhaOfCourse</h4>
        <div>
            <p>Welcome, <?php echo $name; ?></p>
            <a href="/logout.php">Log out</a>
        </div>
    </nav>
    
    <button>Create Course</button>
    <select name="course-selector" id="course-selector">Course Selector</select>
</body>
</html>