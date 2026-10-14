<?php
session_start();

// Only allow admin
if (!isset($_SESSION['user']) || $_SESSION['user']['user'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Validate and sanitize ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Don't allow admin to delete their own account
if ($_SESSION['user']['id'] == $user_id) {
    header("Location: admin_users.php?error=cannot_delete_self");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hackathon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Delete user
$stmt = $conn->prepare("DELETE FROM auth_user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: admin_users.php?deleted=success");
exit();
