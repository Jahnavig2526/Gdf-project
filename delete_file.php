<?php
header('Content-Type: application/json');
session_start();
$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$users = ['admin'=>'1234','user1'=>'123'];

if (!isset($users[$username]) || $users[$username] !== $password) {
    echo json_encode(['success'=>false,'message'=>'Invalid credentials']);
    exit;
}

if (!isset($data['id'])) {
    echo json_encode(['success'=>false,'message'=>'File ID not provided']);
    exit;
}

$fileId = $data['id'];

$host = "localhost";
$user = "root";
$pass = "";
$db = "file_management";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['success'=>false,'message'=>'DB connection failed']);
    exit;
}

$stmt = $conn->prepare("SELECT path FROM files WHERE id = ?");
$stmt->bind_param("i",$fileId);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();

if ($file) {
    if(file_exists($file['path'])) unlink($file['path']);
    $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
    $stmt->bind_param("i",$fileId);
    $stmt->execute();
    echo json_encode(['success'=>true]);
} else echo json_encode(['success'=>false,'message'=>'File not found']);

$conn->close();
?>
