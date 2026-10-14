<?php
session_start();

// Check login
if (!isset($_SESSION['user']['username'])) {
    echo "You must be logged in to view this page.";
    exit;
}

$username = $_SESSION['user']['username'];

// DB connection
$mysqli = new mysqli("localhost", "root", "", "hackathon");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch videos by this user
$query = "SELECT * FROM video WHERE uploaded_by = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Uploaded Videos</title>
    <style>
        .video-card {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h1>🎥 My Uploaded Videos</h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="video-card">
                <h3><?= htmlspecialchars($row['video_name']) ?></h3>
                <p><strong>Uploaded By:</strong> <?= htmlspecialchars($row['uploaded_by']) ?></p>
                <p><strong>Uploaded At:</strong> <?= htmlspecialchars($row['uploaded_at']) ?></p>
                <video width="320" height="240" controls>
                    <source src="<?= htmlspecialchars($row['file_path']) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No videos uploaded yet.</p>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$mysqli->close();
?>
