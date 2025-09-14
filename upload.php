<?php
session_start();

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = '';

if (isset($_POST['upload'])) {
    $targetDir = "uploads/";
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Create folder if not exists
    }

    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    // Check file type (optional)
    $allowedTypes = ['jpg','jpeg','png','pdf','doc','docx','xls','xlsx','txt','zip'];
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    if (in_array(strtolower($fileType), $allowedTypes)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            $message = "File uploaded successfully!";
        } else {
            $message = "Error uploading file!";
        }
    } else {
        $message = "File type not allowed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload File</title>
<style>
body { font-family: Arial; background: #f2f2f2; display: flex; justify-content: center; align-items: center; height: 100vh; }
.container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; text-align: center; }
input[type="file"] { margin: 1rem 0; }
button { padding: 0.7rem 1.2rem; background: #1e90ff; color: white; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background: #145a9b; }
.message { margin-top: 1rem; color: green; }
.back { margin-top: 15px; display: block; color: #333; text-decoration: none; }
</style>
</head>
<body>
<div class="container">
    <h2>Upload File</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <br>
        <button type="submit" name="upload">Upload</button>
    </form>
    <?php if($message != '') { echo "<div class='message'>$message</div>"; } ?>
    <a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
</div>
</body>
</html>
