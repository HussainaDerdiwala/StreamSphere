<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db.php';
require 'django_hasher.php'; // 👉 create this file (code below)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Check if fields are empty
    if (empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: reset_password_form.php");
        exit();
    }

    // Check if passwords match
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: reset_password_form.php");
        exit();
    }

    // Check if user is verified
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Session expired. Please try again.";
        header("Location: login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];

    // ✅ Hash the password using Django-compatible method
    $hashedPassword = django_pbkdf2_sha256($newPassword);

    // Update in database
    $stmt = $conn->prepare("UPDATE auth_user SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $userId]);

    // Clear session and redirect
    unset($_SESSION['user_id'], $_SESSION['otp_verified']);
    $_SESSION['success'] = "Password successfully updated. Please login.";
    header("Location: login.php");
    exit();
} else {
    include 'reset_password_form.php'; // 👉 HTML form page
}
