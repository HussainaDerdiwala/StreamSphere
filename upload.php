<?php
session_start();
$is_authenticated = isset($_SESSION['user']);
$user = $_SESSION['user'] ?? null;

if (!$is_authenticated) {
    header('Location: login.php');
    exit();
}

// DB Connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "hackathon";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoName = $_POST['video_name'];
    $category = $_POST['category'];
    $privacy = isset($_POST['privacy']) ? (int)$_POST['privacy'] : 0; // 0=Public, 1=Private

    $uploadedBy = $user['username'];

    $videoFile = $_FILES['video'];
    $thumbnailFile = $_FILES['thumbnail'];

    $videoDir = 'uploads/videos/';
    $thumbDir = 'uploads/thumbnails/';

    if (!is_dir($videoDir)) mkdir($videoDir, 0777, true);
    if (!is_dir($thumbDir)) mkdir($thumbDir, 0777, true);

    $videoPath = $videoDir . uniqid() . '_' . basename($videoFile['name']);
    $thumbPath = $thumbDir . uniqid() . '_' . basename($thumbnailFile['name']);

    if (move_uploaded_file($videoFile['tmp_name'], $videoPath) && move_uploaded_file($thumbnailFile['tmp_name'], $thumbPath)) {
        $stmt = $conn->prepare("INSERT INTO video (video_name, file_path, thumbnail, uploaded_by, category, privacy) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $videoName, $videoPath, $thumbPath, $uploadedBy, $category, $privacy);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit();
    } else {
        echo "Upload failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Video</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">

<div class="container">
    <h2 class="mb-4">Upload Video</h2>

    <a href="index.php" class="btn btn-secondary mb-4">← Back to Home</a>

    <form method="post" enctype="multipart/form-data">

        <div class="mb-3">
            <label>Video Name</label>
            <input type="text" name="video_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Category</label>
            <select name="category" class="form-control" required>
                <option value="">Select Category</option>
                <option value="Computer programming">Computer programming</option>
                <option value="Music">Music</option>
                <option value="Gaming">Gaming</option>
                <option value="Podcasts">Podcasts</option>
                <option value="Web series">Web series</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Privacy</label>
            <select name="privacy" class="form-control" required>
                <option value="0">Public</option>
                <option value="1">Private</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Video File</label>
            <input type="file" name="video" class="form-control" accept="video/*" required>
        </div>

        <div class="mb-3">
            <label>Thumbnail Image</label>
            <input type="file" name="thumbnail" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload</button>

    </form>
</div>

</body>
</html>
