<?php
header('Content-Type: application/json');
session_start();

// Login check
if (!isset($_POST['username'], $_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];
$users = ['admin'=>'1234','user1'=>'123'];

if (!isset($users[$username]) || $users[$username] !== $password) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

include 'db.php';

// Use $conn from db.php
if ($conn->connect_error) {
    echo json_encode(['success'=>false,'message'=>'DB connection failed']);
    exit;
}

$domain = $_POST['domain'] ?? '';
if (!$domain) {
    echo json_encode(['success'=>false,'message'=>'Domain not specified']);
    exit;
}

$uploadDir = 'uploads/' . $domain . '/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$uploadedFiles = [];
foreach ($_FILES['files']['name'] as $key => $name) {
    $tmpName = $_FILES['files']['tmp_name'][$key];
    $size = $_FILES['files']['size'][$key];
    $type = $_FILES['files']['type'][$key];
    $filename = time() . '_' . basename($name);
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($tmpName, $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO files (name, size, type, uploadDate, domain, path) VALUES (?, ?, ?, NOW(), ?, ?)");
        $stmt->bind_param("sssss", $name, $size, $type, $domain, $targetFile);
        $stmt->execute();
        $uploadedFiles[] = $name;
    }
}

echo json_encode(count($uploadedFiles) > 0 ? ['success'=>true,'uploaded'=>$uploadedFiles] : ['success'=>false,'message'=>'No files uploaded']);
$conn->close();
?>
