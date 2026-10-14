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

$users = $conn->query("SELECT * FROM auth_user ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body { background: #f9f9f9; margin: 0; padding: 0; }

    .sidebar {
      width: 200px;
      background: #fff;
      position: fixed;
      top: 60px;
      left: 0;
      bottom: 0;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
      padding-top: 10px;
      overflow: hidden;
    }

    .sidebar ul { list-style: none; padding: 0; margin: 0; }
    .sidebar li {
      padding: 15px 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      color: #555;
      transition: background 0.3s;
    }

    .sidebar li:hover { background: #f1f1f1; }

    .header {
      padding: 10px 20px;
      background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      height: 60px;
    }

    .logo { font-weight: bold; font-size: 1.5rem; color: red; }

    .main { margin-top: 60px; display: flex; }
    .content-area {
      flex: 1;
      padding: 20px;
      margin-left: 200px;
    }

    .profile-pic {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
    }

    .profile-wrapper { position: relative; cursor: pointer; }
    .dropdown-panel {
      display: none;
      position: absolute;
      top: 45px;
      right: 0;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      border-radius: 5px;
      padding: 10px;
      width: 300px;
      z-index: 999;
    }

    .dropdown-panel .profile-info {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
    }

    .dropdown-panel .profile-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }

    .dropdown-panel a {
      display: block;
      padding: 5px 0;
      color: #333;
      text-decoration: none;
    }

    .dropdown-panel a:hover { background: #f1f1f1; }
  </style>
</head>
<body>

<div class="header">
  <div class="d-flex align-items-center">
    <span class="logo">Streamly Admin</span>
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

<div class="sidebar" id="sidebar">
  <ul>
    <li><i class="bi bi-house-door"></i> <span><a href="admin.php" style="color: inherit; text-decoration: none;">Dashboard</a></span></li>
    <li><i class="bi bi-person"></i> <span><a href="admin_users.php" style="color: inherit; text-decoration: none;">Manage Users</a></span></li>
    <li><i class="bi bi-collection-play"></i> <span><a href="admin_videos.php" style="color: inherit; text-decoration: none;">Manage Videos</a></span></li>
  </ul>
</div>

<div class="main">
  <div class="content-area" id="contentArea">
    <div class="container">
      <h2 class="mb-4">All Registered Users</h2>

      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>User Type</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $users->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['user']) ?></td>
                <td>
                  <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm" style="background-color:gray; color: white;">&nbsp;Edit&nbsp;</a>
                  <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm text-white" style="background-color:gray;" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('profileBtn').addEventListener('click', function(e) {
  e.stopPropagation();
  const dropdown = document.getElementById('dropdownMenu');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

document.addEventListener('click', function(e) {
  const dropdown = document.getElementById('dropdownMenu');
  if (!dropdown.contains(e.target) && !document.getElementById('profileBtn').contains(e.target)) {
    dropdown.style.display = 'none';
  }
});
</script>

</body>
</html>
