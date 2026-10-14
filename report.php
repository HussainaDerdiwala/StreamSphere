<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$video_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($video_id <= 0) {
    echo "Invalid video ID.";
    exit;
}

// Handle form submission
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = $_POST['reason'] ?? '';
    $details = trim($_POST['details'] ?? '');

    if (empty($reason)) {
        $error = "Please select a reason for reporting.";
    } else {
        $conn = new mysqli("localhost", "root", "", "hackathon");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO reports (video_id, user_id, reason, details, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiss", $video_id, $user['id'], $reason, $details);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Report Video</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Report Video</h2>

  <?php if ($success): ?>
    <div class="alert alert-success">Your report has been submitted successfully. Thank you!</div>
  <?php elseif (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label for="reason" class="form-label">Reason for report</label>
      <select name="reason" id="reason" class="form-select" required>
        <option value="">-- Select reason --</option>
        <option value="Inappropriate content">Inappropriate content</option>
        <option value="Spam or misleading">Spam or misleading</option>
        <option value="Violent or repulsive content">Violent or repulsive content</option>
        <option value="Hateful or abusive">Hateful or abusive</option>
        <option value="Copyright issue">Copyright issue</option>
        <option value="Other">Other</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="details" class="form-label">Additional details (optional)</label>
      <textarea name="details" id="details" class="form-control" rows="4" placeholder="Provide more information if necessary..."></textarea>
    </div>

    <button type="submit" class="btn btn-danger">Submit Report</button>
    <a href="watch.php?id=<?= $video_id ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
