<?php
session_start();

if (!isset($_SESSION['user']['username'])) {
    echo "Login required.";
    exit;
}

$username = $_SESSION['user']['username'];
$mysqli = new mysqli("localhost", "root", "", "hackathon");
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$userStmt = $mysqli->prepare("SELECT profile_picture FROM auth_user WHERE username = ?");
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();

$profilePic = $userData['profile_picture'] ?? '';
$avatar = (!empty($profilePic) && file_exists($profilePic)) ? $profilePic : "https://via.placeholder.com/100";

$stmt = $mysqli->prepare("SELECT * FROM video WHERE uploaded_by = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$videos = $stmt->get_result();
$videoCount = $videos->num_rows;

$likesQuery = $mysqli->query("
    SELECT SUM(liked = 1) AS totalLikes
    FROM likes
    WHERE video_id IN (SELECT id FROM video WHERE uploaded_by = '$username')
");
$totalLikes = $likesQuery->fetch_assoc()['totalLikes'] ?? 0;

$subCountQuery = $mysqli->query("SELECT COUNT(*) as total FROM subscriptions WHERE channel_username = '$username'");
$subscriberCount = $subCountQuery->fetch_assoc()['total'] ?? 0;

$subscribersQuery = $mysqli->query("
    SELECT a.username, s.subscriber_id 
    FROM subscriptions s
    JOIN auth_user a ON s.subscriber_id = a.id
    WHERE s.channel_username = '$username'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($username) ?>'s Channel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: var(--bg);
      color: var(--text);
    }
    .channel-banner {
      background: linear-gradient(90deg, #007bff, #6610f2);
      color: white;
      padding: 40px 20px;
      border-radius: 10px;
    }
    .channel-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      border: 4px solid white;
      object-fit: cover;
    }
    .video-card video {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .nav-tabs .nav-link.active {
      background-color: var(--card-bg);
      border-color: #dee2e6 #dee2e6 var(--card-bg);
    }

    /* Theme variables */
    :root {
      --bg: #f8f9fa;
      --text: #212529;
      --card-bg: #ffffff;
    }
    body.dark {
      --bg: #121212;
      --text: #f5f5f5;
      --card-bg: #1e1e1e;
    }
  </style>
</head>
<body>

<div class="container my-4">
  <div class="channel-banner d-flex align-items-center">
    <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="channel-avatar me-3">
    <div>
      <h3 class="mb-1"><?= htmlspecialchars($username) ?>'s Channel</h3>
      <p class="mb-0">🎥 <?= $videoCount ?> Videos | 👍 <?= $totalLikes ?> Likes | 👥 <?= $subscriberCount ?> Subscribers</p>
    </div>
    <div class="ms-auto">
      <a href="edit_profile.php" class="btn btn-outline-light">Edit Channel</a>
    </div>
  </div>

  <ul class="nav nav-tabs mt-4" id="channelTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab">Uploaded Videos</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="subs-tab" data-bs-toggle="tab" data-bs-target="#subs" type="button" role="tab">Subscribers</button>
    </li>
  </ul>

  <div class="tab-content p-3 border border-top-0 rounded-bottom">
    <div class="tab-pane fade show active" id="videos" role="tabpanel">
      <div class="row">
        <?php if ($videoCount > 0): ?>
          <?php while ($row = $videos->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
              <div class="card video-card">
                <video controls>
                  <source src="<?= htmlspecialchars($row['file_path']) ?>" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($row['video_name']) ?></h5>
                  <p class="card-text">📅 <?= htmlspecialchars($row['uploaded_at']) ?></p>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No videos uploaded yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="tab-pane fade" id="subs" role="tabpanel">
      <ul class="list-group">
        <?php if ($subscribersQuery->num_rows > 0): ?>
          <?php while ($sub = $subscribersQuery->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($sub['username']) ?>
              <form method="post" action="remove_subscriber.php" style="margin:0;">
                <input type="hidden" name="subscriber_id" value="<?= $sub['subscriber_id'] ?>">
                <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
              </form>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <li class="list-group-item">No subscribers yet.</li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark") {
    document.body.classList.add("dark");
  }
</script>
</body>
</html>

<?php
$stmt->close();
$userStmt->close();
$mysqli->close();
?>