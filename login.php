<?php
// Start session and include DB connection
session_start();
include 'db.php';

$message = '';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Escape input to prevent SQL injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Check credentials
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $_SESSION['username'] = $username; // Store username in session
        header("Location: dashboard.php"); // Redirect to dashboard
        exit();
    } else {
        $message = "Incorrect username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<style>
body { font-family: Arial; background: #f5faff; display: flex; justify-content: center; align-items: center; height: 100vh; }
.login-box { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; text-align: center; }
input { width: 100%; padding: 0.7rem; margin: 0.5rem 0; border-radius: 5px; border: 1px solid #ccc; }
button { width: 100%; padding: 0.7rem; background: #1e90ff; color: white; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background: #145a9b; }
.message { margin-top: 1rem; color: red; }
</style>
</head>
<body>
<div class="login-box">
<h2>Login</h2>
<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit" name="login">Login</button>
</form>
<?php if($message != ''){ echo "<div class='message'>$message</div>"; } ?>
</div>
</body>
</html>
