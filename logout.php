<?php
session_start();
$_SESSION = [];
session_destroy();

echo "<p>Logging out...</p>";
header("Refresh: 2; URL=login.php"); // Wait 2 seconds before redirecting
exit();
?>
