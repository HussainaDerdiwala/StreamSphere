<?php
session_start();

$conn = new mysqli("localhost", "root", "", "hackathon");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user']['id'] ?? null;
$username = $_SESSION['user']['username'] ?? null;

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$video_id = $_GET['id'];

// Get video details
$stmt = $conn->prepare("SELECT * FROM video WHERE id = ?");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Video not found.";
    exit;
}
$video = $result->fetch_assoc();

// Get like/dislike counts
$likesData = $conn->query("SELECT SUM(liked=1) AS likes, SUM(liked=0) AS dislikes FROM likes WHERE video_id = $video_id")->fetch_assoc();
$likes = $likesData['likes'] ?? 0;
$dislikes = $likesData['dislikes'] ?? 0;

// Get user like/dislike status
$userLike = null;
if ($user_id) {
    $res = $conn->query("SELECT liked FROM likes WHERE user_id = $user_id AND video_id = $video_id");
    $userLike = $res->fetch_assoc();
    $likedByUser = $userLike['liked'] ?? null;
}

// Get subscribers count
$uploader = $video['uploaded_by'];
$subCountRes = $conn->query("SELECT COUNT(*) AS subs FROM subscriptions WHERE channel_username = '$uploader'");
$subscribers = $subCountRes->fetch_assoc()['subs'] ?? 0;

// Check subscription status
$subscribed = false;
if ($user_id) {
    $checkSub = $conn->query("SELECT 1 FROM subscriptions WHERE subscriber_id = $user_id AND channel_username = '$uploader'");
    $subscribed = $checkSub->num_rows > 0;
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    if (isset($_POST['like'])) {
        $stmt = $conn->prepare("SELECT liked FROM likes WHERE user_id = ? AND video_id = ?");
        $stmt->bind_param("ii", $user_id, $video_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $existing = $res->fetch_assoc();

        if ($existing && $existing['liked'] == 1) {
            $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND video_id = ?");
            $stmt->bind_param("ii", $user_id, $video_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("REPLACE INTO likes (user_id, video_id, liked) VALUES (?, ?, 1)");
            $stmt->bind_param("ii", $user_id, $video_id);
            $stmt->execute();
        }
    }

    if (isset($_POST['unlike'])) {
        $stmt = $conn->prepare("SELECT liked FROM likes WHERE user_id = ? AND video_id = ?");
        $stmt->bind_param("ii", $user_id, $video_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $existing = $res->fetch_assoc();

        if ($existing && $existing['liked'] == 0) {
            $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND video_id = ?");
            $stmt->bind_param("ii", $user_id, $video_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("REPLACE INTO likes (user_id, video_id, liked) VALUES (?, ?, 0)");
            $stmt->bind_param("ii", $user_id, $video_id);
            $stmt->execute();
        }
    }

    if (isset($_POST['subscribe'])) {
        $stmt = $conn->prepare("INSERT IGNORE INTO subscriptions (subscriber_id, channel_username) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $uploader);
        $stmt->execute();
    }

    if (isset($_POST['unsubscribe'])) {
        $stmt = $conn->prepare("DELETE FROM subscriptions WHERE subscriber_id = ? AND channel_username = ?");
        $stmt->bind_param("is", $user_id, $uploader);
        $stmt->execute();
    }

    if (isset($_POST['comment']) && !empty(trim($_POST['comment_text']))) {
        $comment = trim($_POST['comment_text']);
        $stmt = $conn->prepare("INSERT INTO comments (video_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $video_id, $user_id, $comment);
        $stmt->execute();
    }

    header("Location: watch.php?id=" . $video_id);
    exit;
}

// Get comments
$comments = $conn->query("
    SELECT c.comment, c.created_at, a.username 
    FROM comments c 
    JOIN auth_user a ON c.user_id = a.id 
    WHERE c.video_id = $video_id 
    ORDER BY c.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($video['video_name']) ?> | Streamly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .video-container {
            max-width: 960px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .video-wrapper {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            background: black;
            border-radius: 10px;
            overflow: hidden;
        }
        .video-wrapper video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .actions { margin-top: 15px; }
        .actions form, .actions button { display: inline; margin-right: 5px; }
        .comment-section { margin-top: 30px; }
    </style>
</head>
<body>

<div class="video-container">
    <h3><?= htmlspecialchars($video['video_name']) ?></h3>

    <div class="video-wrapper" id="videoWrapper">
        <video id="mainVideo" controls autoplay>
            <source src="<?= htmlspecialchars($video['file_path']) ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <p class="mt-2 text-muted">Uploaded by: <?= htmlspecialchars($uploader) ?> | Subscribers: <strong><?= $subscribers ?></strong></p>
    <p>👍 <?= $likes ?> | 👎 <?= $dislikes ?></p>

    <!-- Actions -->
    <div class="actions">
        <?php if ($user_id): ?>
            <form method="post"><button name="like" class="btn <?= (isset($likedByUser) && $likedByUser == 1) ? 'btn-primary' : 'btn-outline-primary' ?>">👍 Like</button></form>
            <form method="post"><button name="unlike" class="btn <?= (isset($likedByUser) && $likedByUser === 0) ? 'btn-secondary' : 'btn-outline-secondary' ?>">👎 Dislike</button></form>
            <button onclick="toggleShareMenu()" class="btn btn-outline-success">🔗 Share</button>
            <form method="post">
                <?php if ($subscribed): ?>
                    <button name="unsubscribe" class="btn btn-danger">Unsubscribe</button>
                <?php else: ?>
                    <button name="subscribe" class="btn btn-outline-danger">Subscribe</button>
                <?php endif; ?>
            </form>
            <button onclick="reportVideo(<?= $video_id ?>)" class="btn btn-outline-warning">🚩 Report</button>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline-primary">👍 Like</a>
            <a href="login.php" class="btn btn-outline-secondary">👎 Dislike</a>
            <button onclick="toggleShareMenu()" class="btn btn-outline-success">🔗 Share</button>
            <a href="login.php" class="btn btn-outline-danger">🔔 Subscribe</a>
            <a href="login.php" class="btn btn-outline-warning">🚩 Report</a>
        <?php endif; ?>
    </div>

    <!-- Share Menu -->
    <div id="shareMenu" style="display: none; position: absolute; background: white; padding: 10px; border: 1px solid #ccc; z-index: 100;">
        <p>Share this video:</p>
        <input type="text" id="videoLink" value="<?= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" readonly style="width: 100%; margin-bottom: 10px;">
        <button onclick="copyToClipboard()">📋 Copy Link</button><br>
    </div>

    <!-- Comments -->
    <div class="comment-section">
        <h5 class="mt-4">Comments</h5>
        <?php if ($user_id): ?>
            <form method="post">
                <textarea class="form-control mb-2" name="comment_text" placeholder="Add a comment..." required></textarea>
                <button name="comment" class="btn btn-primary">Comment</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Login</a> to comment.</p>
        <?php endif; ?>
        <ul class="list-group mt-3">
            <?php while($c = $comments->fetch_assoc()): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($c['username']) ?>:</strong>
                    <p class="mb-1"><?= htmlspecialchars($c['comment']) ?></p>
                    <small class="text-muted"><?= $c['created_at'] ?></small>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <a href="index.php" class="btn btn-secondary mt-3">Back to Home</a>
</div>

<script>
function toggleShareMenu() {
    const menu = document.getElementById('shareMenu');
    menu.style.display = (menu.style.display === 'none') ? 'block' : 'none';
}
function copyToClipboard() {
    const copyText = document.getElementById("videoLink");
    copyText.select();
    document.execCommand("copy");
    alert("Link copied: " + copyText.value);
}
document.addEventListener('click', function(event) {
    const menu = document.getElementById('shareMenu');
    if (!event.target.closest('#shareMenu') && !event.target.closest('button[onclick="toggleShareMenu()"]')) {
        menu.style.display = 'none';
    }
});
function reportVideo(id) {
    if (confirm("Are you sure you want to report this video?")) {
        window.location.href = "report.php?id=" + id;
    }
}
</script>

<?php if ($_SESSION['user']['user_type'] === 'free'): ?>
<script>
const video = document.getElementById('mainVideo');
const wrapper = document.getElementById('videoWrapper');
const adUrl = 'https://www.w3schools.com/html/mov_bbb.mp4';

let adPlayed = false;
let isAdPlaying = false;

video.addEventListener('loadedmetadata', () => {
    const duration = video.duration;
    const randomAdTime = Math.floor(Math.random() * (duration / 2 - 5)) + 5;

    video.addEventListener('timeupdate', () => {
        if (!adPlayed && Math.floor(video.currentTime) === randomAdTime) {
            adPlayed = true;
            video.pause();
            playAd(adUrl, video.currentTime);
        }
    });
});

function playAd(adUrl, resumeTime) {
    if (isAdPlaying) return;
    isAdPlaying = true;

    const adVideo = document.createElement('video');
    adVideo.src = adUrl;
    adVideo.controls = true;
    adVideo.autoplay = true;
    adVideo.style.position = 'absolute';
    adVideo.style.top = '0';
    adVideo.style.left = '0';
    adVideo.style.width = '100%';
    adVideo.style.height = '100%';
    adVideo.style.zIndex = '10';
    adVideo.style.borderRadius = '10px';

    const skipBtn = document.createElement('button');
    skipBtn.textContent = 'Skip Ad';
    skipBtn.style.position = 'absolute';
    skipBtn.style.bottom = '60px';
    skipBtn.style.right = '10px';
    skipBtn.style.zIndex = '20';
    skipBtn.style.display = 'none';
    skipBtn.className = 'btn btn-sm btn-light';

    wrapper.appendChild(adVideo);
    wrapper.appendChild(skipBtn);

    const skipTimeout = setTimeout(() => {
        skipBtn.style.display = 'block';
    }, 5000);

    skipBtn.addEventListener('click', () => {
        clearTimeout(skipTimeout);
        endAd(adVideo, skipBtn, resumeTime);
    });

    adVideo.addEventListener('ended', () => {
        clearTimeout(skipTimeout);
        endAd(adVideo, skipBtn, resumeTime);
    });

    adVideo.play().catch(() => console.warn("Autoplay blocked"));
}

function endAd(adElement, skipBtn, resumeTime) {
    adElement.remove();
    skipBtn.remove();
    video.currentTime = resumeTime + 1;
    video.play();
    isAdPlaying = false;
}
</script>
<?php endif; ?>

</body>
</html>