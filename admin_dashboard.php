<?php
session_start();

// Only allow logged-in users with admin role (role_id = 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, Admin!</h2>
    <p>You have full control over the system.</p>
    <ul>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="manage_files.php">Manage Files</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
