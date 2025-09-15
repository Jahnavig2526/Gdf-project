<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
<h2>Welcome, <?php echo $username; ?>!</h2>

<ul>
    <li><a href="view_files.php">View Files</a></li>
    <li><a href="upload_file.php">Upload Files</a></li>
    <li><a href="download_file.php">Download Files</a></li>
</ul>

<a href="logout.php">Logout</a>
</body>
</html>
