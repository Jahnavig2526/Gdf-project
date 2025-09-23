<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

// Check if user is logged in
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

// Handle different actions
$action = $_GET['action'] ?? '';

switch($action) {
    case 'list_files':
        $domain = $_GET['domain'] ?? '';
        if (!$domain) {
            echo json_encode(['success' => false, 'message' => 'Domain not specified']);
            exit;
        }
        
        $stmt = $conn->prepare("SELECT * FROM files WHERE domain = ? ORDER BY upload_date DESC");
        $stmt->bind_param("s", $domain);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $files = [];
        while ($row = $result->fetch_assoc()) {
            $files[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'size' => $row['size'],
                'type' => $row['type'],
                'uploadDate' => $row['upload_date']
            ];
        }
        echo json_encode(['success' => true, 'files' => $files]);
        break;

    case 'upload':
        checkAuth();
        $domain = $_POST['domain'] ?? '';
        if (!$domain) {
            echo json_encode(['success' => false, 'message' => 'Domain not specified']);
            exit;
        }

        $uploadDir = 'uploads/' . $domain . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploaded = [];
        foreach ($_FILES['files']['name'] as $key => $name) {
            $tmpName = $_FILES['files']['tmp_name'][$key];
            $size = $_FILES['files']['size'][$key];
            $type = $_FILES['files']['type'][$key];
            
            $filename = time() . '_' . basename($name);
            $targetFile = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $targetFile)) {
                $stmt = $conn->prepare("INSERT INTO files (name, size, type, domain, path, upload_date) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("sssss", $name, $size, $type, $domain, $targetFile);
                $stmt->execute();
                $uploaded[] = $name;
            }
        }

        echo json_encode(['success' => true, 'files' => $uploaded]);
        break;

    case 'delete':
        checkAuth();
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'File ID not specified']);
            exit;
        }

        $stmt = $conn->prepare("SELECT path FROM files WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();

        if ($file && file_exists($file['path'])) {
            unlink($file['path']);
        }

        $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo json_encode(['success' => true]);
        break;

    case 'download':
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'File ID not specified']);
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM files WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();

        if (!$file || !file_exists($file['path'])) {
            echo json_encode(['success' => false, 'message' => 'File not found']);
            exit;
        }

        header('Content-Type: ' . $file['type']);
        header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
        header('Content-Length: ' . filesize($file['path']));
        readfile($file['path']);
        exit;
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>