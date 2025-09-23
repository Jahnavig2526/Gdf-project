<?php
header('Content-Type: application/json');

session_start();

// Basic login check
if (!isset($_POST['username'], $_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

// Hardcoded users
$users = [
    'admin' => '1234',
    'user1' => '123'
];

if (!isset($users[$username]) || $users[$username] !== $password) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

include 'db.php';

// Use $conn from db.php
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$domain = $_POST['domain'] ?? '';
$stmt = $conn->prepare("SELECT id, name, size, type, uploadDate FROM files WHERE domain = ? ORDER BY uploadDate DESC");
$stmt->bind_param("s", $domain);
$stmt->execute();
$result = $stmt->get_result();

$files = [];
while ($row = $result->fetch_assoc()) $files[] = $row;

echo json_encode(['success' => true, 'files' => $files]);
$conn->close();
?>
