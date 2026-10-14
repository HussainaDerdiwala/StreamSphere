<?php
session_start();

// Only allow admin
if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Validate and sanitize video ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_reports.php");
    exit();
}

$video_id = intval($_GET['id']);

// Database connection
$conn = new mysqli("localhost", "root", "", "hackathon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Delete associated reports
$stmt1 = $conn->prepare("DELETE FROM reports WHERE video_id = ?");
if ($stmt1) {
    $stmt1->bind_param("i", $video_id);
    $stmt1->execute();
    $stmt1->close();
} else {
    die("Failed to prepare report deletion query: " . $conn->error);
}

// Delete the video
$stmt2 = $conn->prepare("DELETE FROM video WHERE id = ?");
if ($stmt2) {
    $stmt2->bind_param("i", $video_id);
    $stmt2->execute();
    $stmt2->close();
} else {
    die("Failed to prepare video deletion query: " . $conn->error);
}

$conn->close();

header("Location: admin_reports.php?deleted=success");
exit();
?>
