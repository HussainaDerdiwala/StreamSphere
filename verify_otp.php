<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db.php'; // DB connection file

// Prevent direct access
if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_action'])) {
    $_SESSION['error'] = "Direct access not allowed.";
    header("Location: login.php");
    exit();
}

// If POST → handle OTP check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp'] ?? '');
    $actual_otp = trim($_SESSION['otp'] ?? '');
    $action = $_SESSION['otp_action'] ?? '';

    // OTP comparison
    if ($entered_otp !== $actual_otp) {
        $_SESSION['error'] = "Invalid OTP.";
        header("Location: verify_otp.php");
        exit();
    }

    // === Handle Signup
    if ($action === 'signup') {
        $temp_data = $_SESSION['temp_user_data'] ?? null;
        if (!$temp_data) {
            $_SESSION['error'] = "Session expired. Please signup again.";
            header("Location: signup.php");
            exit();
        }

        // ✅ Redirect to signup.php for auto-registration and login
        header("Location: signup.php?auto_signup=1");
        exit();
    }

    // === Handle Login OTP
    elseif ($action === 'login') {
        $email = $_SESSION['email'] ?? '';

        $stmt = $conn->prepare("SELECT id FROM auth_user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $_SESSION['error'] = "No account with that email.";
            header("Location: login.php");
            exit();
        }

        $_SESSION['user_email'] = $email;

        // Final cleanup
        session_regenerate_id(true);
        unset($_SESSION['otp'], $_SESSION['otp_action'], $_SESSION['email']);
        header("Location: loginDone.php");
        exit();
    }

    // === Handle Password Reset
    elseif ($action === 'reset') {
        $email = $_SESSION['email'] ?? '';

        $stmt = $conn->prepare("SELECT id FROM auth_user WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "User does not exist.";
            header("Location: login.php");
            exit();
        }

        $_SESSION['otp_verified'] = true;
        $_SESSION['user_id'] = $user['id'];

        // Clear session and go to reset page
        unset($_SESSION['otp'], $_SESSION['otp_action'], $_SESSION['email'], $_SESSION['temp_user_data']);
        header("Location: reset_password.php");
        exit();
    }

    // Fallback redirect
    session_regenerate_id(true);
    unset($_SESSION['otp'], $_SESSION['otp_action'], $_SESSION['email'], $_SESSION['temp_user_data']);
    header("Location: " . $action . "Done.php");
    exit();
}

// If not POST, show OTP form
include 'verify_otp_form.php';
    