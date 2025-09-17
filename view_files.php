<?php
session_start();

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$targetDir = "uploads/";
$files = [];

// Read files from uploads directory
if (is_dir($targetDir)) {
    $files = array_diff(scandir($targetDir), ['.', '..']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Files</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 30px; }
        .container { background: #fff; padding: 20px; border-radius: 5px; width: 600px; margin: auto; box-shadow: 0px 2px 4px rgba(0,0,0,0.2); }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
        a { text-decoration: none; color: blue; }
        .back { display: block; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Uploaded Files</h2>
        <?php if (!empty($files)) { ?>
            <table>
                <tr>
                    <th>File Name</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($files as $file) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file); ?></td>
                        <td>
                            <a href="uploads/<?php echo urlencode($file); ?>" download>⬇ Download</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No files uploaded yet.</p>
        <?php } ?>
        <a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
