<?php
include 'db.php';

$username = 'admin'; // the username you added
$password = '1234';  // the password you set

$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Login successful! Welcome, $username.";
} else {
    echo "Invalid username or password.";
}
?>
