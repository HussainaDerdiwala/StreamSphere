<?php
session_start();

// ✅ Securely verify Django PBKDF2_SHA256 password
function verifyDjangoPassword($password, $djangoHash) {
    $parts = explode('$', $djangoHash);
    if (count($parts) !== 4 || $parts[0] !== 'pbkdf2_sha256') {
        return false;
    }

    list(, $iterations, $salt, $hash) = $parts;
    $calculated = base64_encode(
        hash_pbkdf2("sha256", $password, $salt, (int)$iterations, 32, true)
    );

    return hash_equals($hash, $calculated);
}

// ✅ Handle login form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // ✅ Database connection
    $mysqli = new mysqli("localhost", "root", "", "hackathon");
    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }

    // ✅ Fetch user by email
    $stmt = $mysqli->prepare("SELECT * FROM auth_user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // ✅ Verify password and login
    if ($user && verifyDjangoPassword($password, $user['password'])) {
        // ✅ Store session info
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'usertype' => $user['usertype']
        ];

        // ✅ Update last_login timestamp
        $now = date('Y-m-d H:i:s');
        $updateStmt = $mysqli->prepare("UPDATE auth_user SET last_login = ? WHERE id = ?");
        $updateStmt->bind_param("si", $now, $user['id']);
        $updateStmt->execute();

        // ✅ Redirect based on role
        if ($user['user'] === 'admin') {
            header("Location: admin.php"); // Admin panel
        } else {
            header("Location: index.php"); // User home page
        }
        exit();
    } else {
        $error = "❌ Invalid email or password.";
    }
}
?>

<!-- ✅ Login Page HTML -->
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="loginSignup.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 400px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        .link {
            margin-top: 10px;
            text-align: center;
        }
        .link a {
            color: #007bff;
            text-decoration: none;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <div class="link">
            <p><a href="forgot_password.php">Forgot Password?</a></p>
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
</body>
</html>
