<?php
session_start();
$is_authenticated = isset($_SESSION['user']);
$user = $_SESSION['user'] ?? null;
$user_type = $user['user_type'] ?? 'free';

$host = "localhost";
$username = "root";
$password = "";
$dbname = "hackathon";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Streamly</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
body { background: #f9f9f9; margin: 0; padding: 0; }

.sidebar { width: 200px; background: #fff; position: fixed; top: 60px; left: 0; bottom: 0; box-shadow: 2px 0 5px rgba(0,0,0,0.1); padding-top: 10px; overflow: hidden; z-index: 999; transition: width 0.3s; }
.sidebar ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; }
.sidebar li { padding: 15px 20px; display: flex; align-items: center; gap: 10px; cursor: pointer; color: #555; transition: background 0.3s; }
.sidebar li:hover { background: #f1f1f1; }
.sidebar li.active { background: #f1f1f1; }

.header { padding: 10px 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; height: 60px; flex-wrap: wrap; }

.menu-toggle { font-size: 1.5rem; cursor: pointer; margin-right: 15px; }
.logo { font-weight: bold; font-size: 1.5rem; color: red; }

.auth-buttons { display: flex; align-items: center; gap: 10px; position: relative; }
.profile-wrapper { position: relative; cursor: pointer; }
.profile-pic { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 1px solid #ccc; }

.dropdown-panel { display: none; position: absolute; top: 45px; right: 0; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2); border-radius: 5px; padding: 10px; width: 300px; z-index: 999; }
.dropdown-panel .profile-info { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.dropdown-panel .profile-info img { width: 40px; height: 40px; border-radius: 50%; }
.dropdown-panel a { display: block; padding: 5px 0; color: #333; text-decoration: none; }
.dropdown-panel a:hover { background: #f1f1f1; }

.main { margin-top: 60px; display: flex; }
.content-area { flex: 1; padding: 20px; margin-left: 200px; transition: margin-left 0.3s; }
.categories { padding: 10px 20px; overflow-x: auto; white-space: nowrap; }
.categories span { display: inline-block; margin-right: 10px; padding: 5px 15px; background: #e3e3e3; border-radius: 20px; cursor: pointer; transition: background 0.3s; }
.categories span:hover { background: #d4d4d4; }

.card { border: none; border-radius: 10px; overflow: hidden; transition: transform 0.2s; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.card:hover { transform: scale(1.03); }

.video-wrapper { position: relative; width: 100%; height: 200px; overflow: hidden; border-radius: 10px; }
.video-wrapper img.thumbnail, .video-wrapper video.video-preview { width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; transition: opacity 0.5s ease; }
.video-wrapper img.thumbnail { opacity: 1; z-index: 2; }
.video-wrapper video.video-preview { opacity: 0; z-index: 1; }

/* Sidebar collapsed */
.sidebar.collapsed { width: 70px; }
.sidebar.collapsed li span { display: none; }
.content-area.collapsed { margin-left: 70px; }

/* Dark Mode */
body.dark-mode { background: #121212; color: #fff; }
body.dark-mode .sidebar { background: #1e1e1e; }
body.dark-mode .sidebar li { color: #ccc; }
body.dark-mode .sidebar li:hover { background: #2a2a2a; }
body.dark-mode .categories span { background: #333; color: #fff; }
body.dark-mode .categories span:hover { background: #444; }
body.dark-mode .card { background: #1e1e1e; color: #fff; }
body.dark-mode .dropdown-panel { background: #2a2a2a; color: #fff; }
body.dark-mode .header { background: #1e1e1e; }
body.dark-mode .header input[type="text"] { background: #2a2a2a; color: #fff; border: 1px solid #444; }
body.dark-mode .btn-danger { background: #dc3545; }
body.dark-mode .btn-login, body.dark-mode .btn-signup { background: #0d6efd; color: #fff; }
body.dark-mode .dropdown-panel a { color: #fff; }
body.dark-mode .dropdown-panel a:hover { background: #333; }

.theme-toggle-wrapper { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
.switch { position: relative; display: inline-block; width: 46px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #ccc; transition: 0.4s; border-radius: 24px; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background: white; transition: 0.4s; border-radius: 50%; }
input:checked + .slider { background: #2196F3; }
input:checked + .slider:before { transform: translateX(22px); }
</style>
</head>

<body>

<!-- Header -->
<div class="header">
  <div class="d-flex align-items-center">
    <span class="menu-toggle" id="menuToggle">☰</span>
    <span class="logo">Streamly</span>
  </div>

  <div class="flex-grow-1 mx-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search videos...">
  </div>

  <div class="auth-buttons">
    <?php if ($is_authenticated): ?>
      <a href="upload.php"><button class="btn btn-danger">Upload</button></a>
      <?php
        $profile_picture = !empty($user['profile_picture']) && file_exists($user['profile_picture'])
          ? $user['profile_picture']
          : 'uploads/profile_pics/default-avatar.png';
      ?>
      <div class="profile-wrapper" id="profileBtn">
        <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile" class="profile-pic" />
        <div class="dropdown-panel" id="dropdownMenu">
          <div class="profile-info">
            <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile">
            <div>
              <strong><?= htmlspecialchars($user['username']) ?></strong><br>
              <small><?= htmlspecialchars($user['email']) ?></small><br>
              <small>User Type: <?= htmlspecialchars($user['user_type'] ?? 'free') ?></small>
            </div>
          </div>
          <div class="theme-toggle-wrapper">
            <label class="switch">
              <input type="checkbox" id="theme-toggle">
              <span class="slider"></span>
            </label>
            <span id="theme-label">Dark Mode</span>
          </div>
          <a href="view_your_channel.php">View your channel</a>
          <a href="edit_profile.php">Profile Picture</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    <?php else: ?>
      <a href="signup.php"><button class="btn btn-signup">Sign Up</button></a>
      <a href="login.php"><button class="btn btn-login">Login</button></a>
    <?php endif; ?>
  </div>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <ul>
    <li onclick="goToPage('index.php')" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>"><i class="bi bi-house-door"></i> <span>Home</span></li>
    <li onclick="goToPage('subscriptions.php')" class="<?= $currentPage == 'subscriptions.php' ? 'active' : '' ?>"><i class="bi bi-collection-play"></i> <span>Subscriptions</span></li>
    <li onclick="goToPage('view_your_channel.php')" class="<?= $currentPage == 'view_your_channel.php' ? 'active' : '' ?>"><i class="bi bi-person"></i> <span>You</span></li>
  </ul>
</div>

<!-- Main Content -->
<div class="main">
  <div class="content-area" id="contentArea">
    <div class="categories mb-3">
      <span data-category="">All</span>
      <span data-category="Gaming">Gaming</span>
      <span data-category="Music">Music</span>
      <span data-category="Podcasts">Podcasts</span>
      <span data-category="Web Series">Web Series</span>
    </div>

    <div class="row" id="videoContainer"></div>
  </div>
</div>

<script>
$(document).ready(function(){
  function loadVideos(query = '', category = '') {
    $.ajax({
      url: 'search_videos.php',
      method: 'POST',
      data: { search: query, category: category },
      success: function(data) {
        $('#videoContainer').html(data);
      }
    });
  }

  loadVideos();

  $('#searchInput').on('keyup', function(){
    let query = $(this).val();
    loadVideos(query);
  });

  $('.categories span').click(function(){
    let category = $(this).data('category');
    loadVideos('', category);
  });

  $(document).on('mouseenter', '.video-wrapper', function() {
    $(this).find('.thumbnail').css('opacity', 0);
    let video = $(this).find('video')[0];
    video.play();
    $(this).find('video').css('opacity', 1);
  });

  $(document).on('mouseleave', '.video-wrapper', function() {
    let video = $(this).find('video')[0];
    video.pause();
    video.currentTime = 0;
    $(this).find('video').css('opacity', 0);
    $(this).find('.thumbnail').css('opacity', 1);
  });

  // Profile dropdown
  const profileBtn = document.getElementById('profileBtn');
  const dropdownMenu = document.getElementById('dropdownMenu');
  profileBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
  });
  document.addEventListener('click', function(e) {
    if (!dropdownMenu.contains(e.target) && !profileBtn.contains(e.target)) {
      dropdownMenu.style.display = 'none';
    }
  });

  // Dark Mode
  const toggleInput = document.getElementById('theme-toggle');
  const themeLabel = document.getElementById('theme-label');
  function setTheme(isDark) {
    if (isDark) {
      document.body.classList.add('dark-mode');
      themeLabel.textContent = 'Dark Mode';
      toggleInput.checked = true;
      localStorage.setItem('theme', 'dark');
    } else {
      document.body.classList.remove('dark-mode');
      themeLabel.textContent = 'Dark Mode';
      toggleInput.checked = false;
      localStorage.setItem('theme', 'light');
    }
  }
  const savedTheme = localStorage.getItem('theme');
  setTheme(savedTheme === 'dark');

  toggleInput.addEventListener('change', function() {
    setTheme(toggleInput.checked);
  });

  // Hamburger Menu Toggle
  $('#menuToggle').on('click', function(){
    $('#sidebar').toggleClass('collapsed');
    $('#contentArea').toggleClass('collapsed');
  });

});

// Page navigation
function goToPage(url) {
  window.location.href = url;
}
</script>

</body>
</html>