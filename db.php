<?php
$host = "localhost";
$user = "root";
$password = ""; // XAMPP default
$database = "file_management";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_errno) {
    // Connection failed
    die("Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error);
}

// Optional: Uncomment to confirm connection
// echo "Database connected successfully!";
?>
