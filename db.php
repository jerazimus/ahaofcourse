<?php
// db.php
function logUserInDB($pdo, $googleId, $email, $name) {
    // Check if the user already exists based on google_id or email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE google_id = ? OR email = ?');
    $stmt->execute([$googleId, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // User exists, update last login time
        $stmt = $pdo->prepare('UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        $stmt->execute([$user['id']]);
    } else {
        // New user, insert into the database
        // Allow NULL for google_id when logging in the test user
        $stmt = $pdo->prepare('INSERT INTO users (google_id, email, name, created_at, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)');
        
        // If googleId is null, pass null, else pass the googleId
        $stmt->execute([$googleId ?? null, $email, $name]);
    }
}