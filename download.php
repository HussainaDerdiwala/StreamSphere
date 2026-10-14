<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_type = $_SESSION['user']['user_type'] ?? 'free';

if ($user_type !== 'premium') {
    echo "<script>alert('Only premium users can download videos.'); window.history.back();</script>";
    exit;
}

if (!isset($_GET['file'])) {
    echo "Invalid request.";
    exit;
}

$file = basename($_GET['file']);  // Prevent directory traversal
$filePath = "uploads/" . $file;    // Adjust path according to your uploads folder

if (!file_exists($filePath)) {
    echo "File not found.";
    exit;
}

// Force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
?>
