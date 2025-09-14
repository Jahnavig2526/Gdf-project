<?php
session_start();

// Only allow logged-in users with user role (role_id = 2)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome, User!</h2>
    <p>You can upload and view files.</p>
    <ul>
        <li><a href="upload_file.php">Upload File</a></li>
        <li><a href="view_files.php">View Files</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
