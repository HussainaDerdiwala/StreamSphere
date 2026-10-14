<?php
session_start();

// Check if admin is authenticated
if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'a') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hackathon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get user ID to edit
$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header("Location: admin_users.php");
    exit();
}

// Fetch existing user data
$stmt = $conn->prepare("SELECT * FROM auth_user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

if (!$user_data) {
    echo "User not found.";
    exit();
}

$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $usertype = $_POST['usertype'];

    if (empty($username) || empty($email) || empty($usertype)) {
        $error = "All fields are required.";
    } else {
        $update = $conn->prepare("UPDATE auth_user SET username = ?, email = ?, usertype = ? WHERE id = ?");
        $update->bind_param("sssi", $username, $email, $usertype, $user_id);
        if ($update->execute()) {
            $success = "User updated successfully.";
            // Refresh user data
            $stmt->execute();
            $user_data = $stmt->get_result()->fetch_assoc();
            header("location:admin_users.php");
        } else {
            $error = "Failed to update user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <a href="admin_users.php" class="btn btn-secondary mb-4">&larr; Back to Users</a>

    <div class="card shadow-sm">
        <div class="card-header bg-black text-white">
            <h4>Edit User</h4>
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user_data['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">User Type</label>
                    <select name="usertype" class="form-select" required>
                        <option value="admin" <?= $user_data['usertype'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="creator" <?= $user_data['usertype'] === 'creator' ? 'selected' : '' ?>>Creator</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary">Update User</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
