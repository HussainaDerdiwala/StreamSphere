<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "hackathon";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM video WHERE 1 ";

if (!empty($category) && $category != 'All') {
    $sql .= "AND category = '" . $conn->real_escape_string($category) . "' ";
}

if (!empty($search)) {
    $sql .= "AND video_name LIKE '%" . $conn->real_escape_string($search) . "%' ";
}

$sql .= "ORDER BY id DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($video = $result->fetch_assoc()) {
        echo '<div class="col-md-4 col-sm-6 col-12 mb-4">
            <a href="watch.php?id='.$video['id'].'" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="video-wrapper">
                        <img src="'.htmlspecialchars($video['thumbnail']).'" class="thumbnail" alt="'.htmlspecialchars($video['video_name']).'">
                        <video class="video-preview" muted preload="metadata">
                            <source src="'.htmlspecialchars($video['file_path']).'" type="video/mp4">
                        </video>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">'.htmlspecialchars($video['video_name']).'</h5>
                        <p class="card-text">Uploaded by: '.htmlspecialchars($video['uploaded_by']).'</p>
                    </div>
                </div>
            </a>
        </div>';
    }
} else {
    echo '<p>No videos found.</p>';
}

$conn->close();
?>
