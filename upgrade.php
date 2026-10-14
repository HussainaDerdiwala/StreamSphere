<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "hackathon");

$userId = $_SESSION['user']['id'] ?? null;
if ($userId) {
    $stmt = $mysqli->prepare("UPDATE auth_user SET user_type = 'premium' WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Update session immediately
    $_SESSION['user']['user_type'] = 'premium';

    echo "✅ Your account has been upgraded to premium!";
} else {
    echo "❌ You must be logged in.";
}
