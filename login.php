<?php
session_start();
include 'db.php'; // DB connection

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if this is an AJAX request
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if ($contentType === 'application/json') {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        header('Content-Type: application/json');
        
        switch ($action) {
            case 'check_auth':
                if (isset($_SESSION['user_id'])) {
                    echo json_encode([
                        'success' => true,
                        'authenticated' => true,
                        'user' => $_SESSION['username']
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'authenticated' => false
                    ]);
                }
                exit;
                
            case 'login':
                $username = $conn->real_escape_string($input['username']);
                $password = $conn->real_escape_string($input['password']);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                exit;
        }
    } else {
        // Regular form submission
        $username = $conn->real_escape_string($_POST['username']);
        $password = $conn->real_escape_string($_POST['password']);
    }

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
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
/* Reset some defaults */
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #1e90ff, #00bfff);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    background: #fff;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    width: 350px;
    text-align: center;
}

.login-box h2 {
    margin-bottom: 25px;
    color: #333;
}

.login-box input {
    width: 100%;
    padding: 12px 15px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
}

.login-box input:focus {
    border-color: #1e90ff;
    outline: none;
}

.login-box button {
    width: 100%;
    padding: 12px;
    background: #1e90ff;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 15px;
    transition: 0.3s;
}

.login-box button:hover {
    background: #0b5fa5;
}

.message {
    margin-top: 15px;
    color: red;
    font-weight: bold;
}
</style>
</head>
<body>
<div class="login-box">
    <h2>Login</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <?php if($message != '') { echo "<div class='message'>$message</div>"; } ?>
</div>
</body>
</html>
