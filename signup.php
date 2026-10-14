<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db.php';
require 'mailer.php';
require 'django_hasher.php';

$error = '';
$success = '';

// If OTP just got verified and user is in temp session — handle registration and auto-login
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['auto_signup']) && $_GET['auto_signup'] === '1' && isset($_SESSION['temp_user_data'])) {
    $temp_user = $_SESSION['temp_user_data'];

    // Insert the user
    $stmt = $conn->prepare("INSERT INTO auth_user (username, email, password) VALUES (:username, :email, :password)");
    $stmt->execute([
        'username' => $temp_user['username'],
        'email' => $temp_user['email'],
        'password' => $temp_user['password']
    ]);

    // Auto-login logic
    $user_id = $conn->lastInsertId();
    $query = $conn->prepare("SELECT * FROM auth_user WHERE id = :id");
    $query->execute(['id' => $user_id]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    $_SESSION['user'] = $user;

    // Clean up
    unset($_SESSION['temp_user_data'], $_SESSION['otp'], $_SESSION['otp_action']);

    // Redirect to homepage
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if user already exists
        $stmt = $conn->prepare("SELECT id FROM auth_user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $error = "An account already exists with that email.";
        } else {
            // Hash the password using Django-style hash
            $hashed_password = django_pbkdf2_sha256($password, null, 600000);

            // Generate OTP
            $otp = rand(100000, 999999);

            // Store data in session temporarily
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_action'] = 'signup';
            $_SESSION['temp_user_data'] = [
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password
            ];

            // Send OTP
            sendOtpMail($email, $otp);

            // Redirect to OTP page
            header("Location: verify_otp.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Signup</title>
    <link rel="stylesheet" href="loginSignup.css">
</head>

<body>
    <div class="container">
        <div class="form-box hidden" id="signup-box">
            <h2>Sign Up</h2>

            <?php if ($error): ?>
                <p style="color: red"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="username" placeholder="Username" required><br><br>
                <input type="email" name="email" placeholder="Email" required><br><br>
                <input type="password" name="password" placeholder="Password" required><br><br>
                <button type="submit">Signup</button>
                <p>or</p>
                <div id="g_id_onload"
                    data-client_id="178527337826-llut4r12k2j1487vus0i0u1aeb7egbak.apps.googleusercontent.com"
                    data-callback="handleCredentialResponse">
                </div>
                <div class="g_id_signin" data-type="standard"></div>
                <p>Already have an account? <a href="login.php" onclick="showLogin()">Login</a></p>
            </form>
        </div>
        <p><a href="index.php" class="home-link">← Back to Home</a></p>
    </div>
</body>

</html>
