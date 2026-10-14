<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

$conn = new mysqli("localhost", "root", "", "hackathon");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    $file = $_FILES["profile_picture"];
    $target_dir = "uploads/profile_pics/";
    $file_name = basename($file["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_types = ["jpg", "jpeg", "png"];
    if (in_array($imageFileType, $allowed_types)) {
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE auth_user SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $target_file, $user_id);
            if ($stmt->execute()) {
                $_SESSION['user']['profile_picture'] = $target_file;
                $success = "Profile picture saved successfully.";
            } else {
                $error = "Database update failed.";
            }
            $stmt->close();
        } else {
            $error = "File move failed.";
        }
    } else {
        $error = "Invalid file type. Only JPG, JPEG, and PNG allowed.";
    }
}

// Fetch updated info
$stmt = $conn->prepare("SELECT username, profile_picture FROM auth_user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$updated_user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$hasProfilePic = !empty($updated_user['profile_picture']) && file_exists($updated_user['profile_picture']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $hasProfilePic ? "Edit Profile Picture" : "Upload Profile Picture" ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-avatar-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #007bff;
        }
    </style>
</head>
<body class="container mt-5">

    <h2><?= $hasProfilePic ? "Edit Profile Picture" : "Upload Profile Picture" ?></h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($hasProfilePic): ?>
        <div class="mb-3">
            <label class="form-label">Current Profile Picture:</label><br>
            <img src="<?= htmlspecialchars($updated_user['profile_picture']) ?>" alt="Profile Picture" class="profile-avatar-preview mb-3">
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="profile_picture" class="form-label"><?= $hasProfilePic ? "Choose new profile picture:" : "Choose your profile picture:" ?></label>
            <input class="form-control" type="file" name="profile_picture" id="profile_picture" accept=".jpg,.jpeg,.png" required>
        </div>

        <button type="submit" class="btn btn-primary"><?= $hasProfilePic ? "Update Picture" : "Upload Picture" ?></button>
        <a href="index.php" class="btn btn-secondary">Back to Home</a>
    </form>

</body>
</html>
        