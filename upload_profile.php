<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$userId = $user['id'];

$targetDir = "uploads/profile_pics/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
    $fileName = basename($_FILES['profile_pic']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileExt, $allowedExts)) {
        die("Only image files (JPG, PNG, GIF) are allowed.");
    }

    $newFileName = uniqid('profile_', true) . '.' . $fileExt;
    $destPath = $targetDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // ✅ Update DB
        $conn = new mysqli("localhost", "root", "", "hackathon");
        if ($conn->connect_error) {
            die("DB connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE auth_user SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $destPath, $userId);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        // ✅ Update session
        $_SESSION['user']['profile_pic'] = $destPath;

        header("Location: edit_profile.php");
        exit();
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "No image uploaded or upload error.";
}
