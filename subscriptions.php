<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$conn = new mysqli("localhost", "root", "", "hackathon");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("
    SELECT a.username, a.profile_picture, a.id
    FROM subscriptions s
    JOIN auth_user a ON s.channel_username = a.username
    WHERE s.subscriber_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Subscriptions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: var(--bg);
      color: var(--text);
    }
    .subscription-card {
      background: var(--card-bg);
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      transition: all 0.3s;
    }
    .subscription-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .profile-pic {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
      border: 2px solid #ddd;
    }
    .username {
      font-size: 1.2rem;
      color: var(--text);
      text-decoration: none;
    }

    /* Light & Dark Theme Variables */
    :root {
      --bg: #f5f5f5;
      --text: #111;
      --card-bg: #fff;
    }
    body.dark {
      --bg: #121212;
      --text: #f5f5f5;
      --card-bg: #1e1e1e;
    }
  </style>
</head>
<body class="container my-4">

<h2 class="mb-4">My Subscriptions</h2>

<?php if ($result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <a href="view_channel.php?username=<?= urlencode($row['username']) ?>" class="subscription-card text-decoration-none">
      <img src="<?= !empty($row['profile_picture']) && file_exists($row['profile_picture']) ? htmlspecialchars($row['profile_picture']) : 'https://via.placeholder.com/60' ?>" class="profile-pic" alt="Profile Picture">
      <span class="username"><?= htmlspecialchars($row['username']) ?></span>
    </a>
  <?php endwhile; ?>
<?php else: ?>
  <div class="alert alert-info">You haven't subscribed to any channels yet.</div>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary mt-3">Back to Home</a>

<script>
  // Apply dark mode from localStorage
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark") {
    document.body.classList.add("dark");
  }
</script>
</body>
</html>