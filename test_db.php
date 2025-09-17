<?php
$conn = new mysqli("localhost", "root", "", "file_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✅ Database connected successfully!";
?>
