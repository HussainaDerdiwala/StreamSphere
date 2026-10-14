<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "hackathon";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Failed: " . $conn->connect_error);
}

$search = $_POST['search'] ?? '';
$category = $_POST['category'] ?? '';

$sql = "SELECT * FROM video WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND video_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$output = '';
if ($result->num_rows > 0) {
    while($video = $result->fetch_assoc()) {
        $output .= '
        <div class="col-md-4 col-sm-6 col-12 mb-4">
            <a href="watch.php?id='.$video['id'].'" style="text-decoration:none;color:inherit;">
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
    $output = '<p>No videos found.</p>';
}

echo $output;
$conn->close();
?>
