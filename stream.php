<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$dbname = "hackathon";
$conn = new mysqli($host, $username, $password, $dbname);

$video_id = $_GET['id'];
$user_type = $_SESSION['user']['user_type'] ?? 'free';

$sql = "SELECT file_path FROM video WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $video_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

$file = $res['file_path'];

if ($user_type === 'free') {
    // Prevent download by setting headers without Content-Disposition
    header('Content-Type: video/mp4');
} else {
    // Allow download for premium users
    header('Content-Type: video/mp4');
    header('Content-Disposition: inline; filename="' . basename($file) . '"');
}

readfile($file);
exit;
?>
