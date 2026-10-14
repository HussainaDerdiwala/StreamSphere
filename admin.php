<?php
session_start();
$is_authenticated = isset($_SESSION['user']);
$user = $_SESSION['user'] ?? null;
if (!$is_authenticated) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hackathon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$user_count = $conn->query("SELECT COUNT(*) AS total FROM auth_user")->fetch_assoc()['total'];
$video_count = $conn->query("SELECT COUNT(*) AS total FROM video")->fetch_assoc()['total'];
$report_count = $conn->query("SELECT COUNT(*) AS total FROM reports")->fetch_assoc()['total'];
$recent_uploads = $conn->query("SELECT * FROM video ORDER BY uploaded_at DESC LIMIT 5");
$recent_users = $conn->query("SELECT * FROM auth_user ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Panel - Streamly</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body { background: #f9f9f9; margin: 0; padding: 0; }
    .sidebar {
      width: 200px; background: #fff; position: fixed; top: 60px; left: 0; bottom: 0;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1); padding-top: 10px; transition: width 0.3s; overflow: hidden;
    }
    .sidebar.collapsed { width: 70px; }
    .sidebar ul { list-style: none; padding: 0; margin: 0; }
    .sidebar li {
      padding: 15px 20px; display: flex; align-items: center; gap: 10px;
      cursor: pointer; color: #555; transition: background 0.3s;
    }
    .sidebar li:hover { background: #f1f1f1; }
    .sidebar.collapsed li span { display: none; }

    .header {
      padding: 10px 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      display: flex; justify-content: space-between; align-items: center;
      position: fixed; top: 0; left: 0; right: 0; z-index: 1000; height: 60px;
    }

    .logo { font-weight: bold; font-size: 1.5rem; color: red; }

    .auth-buttons { display: flex; align-items: center; gap: 10px; position: relative; }
    .profile-wrapper { position: relative; cursor: pointer; }
    .profile-pic {
      width: 35px; height: 35px; border-radius: 50%; object-fit: cover;
    }

    .dropdown-panel {
      display: none; position: absolute; top: 45px; right: 0; background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2); border-radius: 5px;
      padding: 10px; width: 300px; z-index: 999;
    }
    .dropdown-panel .profile-info {
      display: flex; align-items: center; gap: 10px; margin-bottom: 10px;
    }
    .dropdown-panel .profile-info img { width: 40px; height: 40px; border-radius: 50%; }
    .dropdown-panel a { display: block; padding: 5px 0; color: #333; text-decoration: none; }
    .dropdown-panel a:hover { background: #f1f1f1; }

    .main { margin-top: 60px; display: flex; }
    .content-area { flex: 1; padding: 20px; margin-left: 200px; transition: margin-left 0.3s; }
    .content-area.collapsed { margin-left: 70px; }

    .card { border: none; border-radius: 10px; overflow: hidden;
      transition: transform 0.2s; background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .card:hover { transform: scale(1.03); }
  </style>
</head>

<body>

<!-- Header -->
<div class="header">
  <div class="d-flex align-items-center">
    <span class="logo">Streamly</span>
  </div>
  
  <div class="auth-buttons">
    <?php
      $profile_picture = !empty($user['profile_picture']) && file_exists($user['profile_picture'])
        ? $user['profile_picture']
        : 'uploads/profile_pics/default-avatar.png';
    ?>
    <div class="profile-wrapper">
      <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile" class="profile-pic" id="profileBtn" />
      <div class="dropdown-panel" id="dropdownMenu">
        <div class="profile-info">
          <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile">
          <div>
            <strong><?= htmlspecialchars($user['username']) ?></strong><br>
            <small><?= htmlspecialchars($user['email']) ?></small>
          </div>
        </div>
        <a href="edit_admin_profile.php">Profile Picture</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <ul>
    <li><i class="bi bi-house-door"></i> <span><a href="admin.php" style="color: inherit; text-decoration: none;">Dashboard</a></span></li>
    <li><i class="bi bi-person"></i> <span><a href="admin_users.php" style="color: inherit; text-decoration: none;">Manage Users</a></span></li>
    <li><i class="bi bi-collection-play"></i> <span><a href="admin_videos.php" style="color: inherit; text-decoration: none;">Manage Videos</a></span></li>
    <li><i class="bi bi-flag"></i> <span><a href="admin_reports.php" style="color: inherit; text-decoration: none;">Report</a></span></li>
    
  </ul>
</div>

<!-- Dashboard Content -->
<div class="main">
  <div class="content-area" id="contentArea">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card text-black bg-white">
            <div class="card-body">
              <h5><i class="bi bi-person"></i> Users</h5>
              <h3><?= $user_count ?></h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-black bg-white">
            <div class="card-body">
              <h5><i class="bi bi-collection-play"></i> Videos</h5>
              <h3><?= $video_count ?></h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-black bg-white">
            <div class="card-body">
              <h5><i class="bi bi-flag"></i> Reported Content</h5>
              <h3><?= $report_count ?></h3>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-light">
              <h5 class="mb-0"><i class="bi bi-upload"></i> Recent Uploads</h5>
            </div>
            <ul class="list-group list-group-flush">
              <?php while ($video = $recent_uploads->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span><?= htmlspecialchars($video['video_name']) ?></span>
                  <span class="badge bg-primary">By <?= htmlspecialchars($video['uploaded_by']) ?></span>
                </li>
              <?php endwhile; ?>
            </ul>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-light">
              <h5 class="mb-0"><i class="bi bi-person"></i> Recently Registered Users</h5>
            </div>
            <ul class="list-group list-group-flush">
              <?php while ($u = $recent_users->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span><?= htmlspecialchars($u['username']) ?></span>
                  <span class="badge bg-secondary"><?= htmlspecialchars($u['email']) ?></span>
                </li>
              <?php endwhile; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');
  const contentArea = document.getElementById('contentArea');
  const profileBtn = document.getElementById('profileBtn');
  const dropdownMenu = document.getElementById('dropdownMenu');

  if (profileBtn && dropdownMenu) {
    profileBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', function(e) {
      if (!dropdownMenu.contains(e.target) && !profileBtn.contains(e.target)) {
        dropdownMenu.style.display = 'none';
      }
    });
  }
});
</script>

</body>
</html>