<?php
$username = $_GET['username'] ?? '';
$password = $_GET['password'] ?? '';
$users = ['admin'=>'1234','user1'=>'123'];

if (!isset($users[$username]) || $users[$username] !== $password) {
    exit('Invalid credentials');
}

$fileId = $_GET['id'] ?? '';
if (!$fileId) exit('File ID missing');

$host = "localhost";
$user = "root";
$pass = "";
$db = "file_management";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) exit('DB connection failed');

$stmt = $conn->prepare("SELECT name,path,type FROM files WHERE id=?");
$stmt->bind_param("i",$fileId);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();

if($file && file_exists($file['path'])) {
    header('Content-Description: File Transfer');
    header('Content-Type: '.$file['type']);
    header('Content-Disposition: attachment; filename="'.basename($file['name']).'"');
    header('Content-Length: '.filesize($file['path']));
    readfile($file['path']);
    exit;
} else exit('File not found');

$conn->close();
?>
