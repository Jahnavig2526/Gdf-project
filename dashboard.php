<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php"); // Redirect if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<style>
body { font-family: Arial; background: #f5faff; margin:0; }
header { background: #1e90ff; color: white; padding: 1rem; text-align: center; }
.container { max-width: 1200px; margin: 2rem auto; padding: 1rem; }
a.button { display: inline-block; margin: 1rem 0; padding: 0.5rem 1rem; background: #1e90ff; color: white; text-decoration: none; border-radius: 5px; }
a.button:hover { background: #145a9b; }
</style>
</head>
<body>
<header>
    <h1>Welcome to Dashboard</h1>
    <p>Hello, <?php echo $_SESSION['username']; ?>!</p>
    <a href="logout.php" class="button">Logout</a>
</header>

<div class="container">
    <h2>Available Modules</h2>
    <ul>
        <li><a href="upload.php" class="button">Upload Files</a></li>
        <li><a href="view_uploads.php" class="button">View Uploaded Files</a></li>
        <!-- Add more links to other modules -->
    </ul>
</div>
</body>
</html>
