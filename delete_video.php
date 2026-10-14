<?php
session_start();
$is_authenticated = isset($_SESSION['user']);
if (!$is_authenticated) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hackathon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$video_id = $_GET['id'] ?? null;

if (!$video_id) {
    header("Location: admin_videos.php?error=no_id_provided");
    exit();
}

// Optional: Get video path if you want to delete the physical file
$video = $conn->prepare("SELECT * FROM video WHERE id = ?");
$video->bind_param("i", $video_id);
$video->execute();
$result = $video->get_result();
$video_data = $result->fetch_assoc();

if (!$video_data) {
    header("Location: admin_videos.php?error=not_found");
    exit();
}

// If you store file paths and want to remove the actual file:
// $file_path = $video_data['video_path'];
// if (file_exists($file_path)) {
//     unlink($file_path); // Delete the file from the server
// }

$delete = $conn->prepare("DELETE FROM video WHERE id = ?");
$delete->bind_param("i", $video_id);

if ($delete->execute()) {
    header("Location: admin_videos.php?deleted=success");
    exit();
    
} else {
    header("Location: admin_videos.php?error=delete_failed");
    exit();
}
?>
