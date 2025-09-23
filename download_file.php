<?php
// filepath: download_file.php
require_once 'db.php';

session_start();

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('File ID not provided');
}

$fileId = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
    $stmt->execute([$fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file) {
        http_response_code(404);
        exit('File not found');
    }
    
    $filePath = 'uploads/' . $file['file_name'];
    
    if (!file_exists($filePath)) {
        http_response_code(404);
        exit('Physical file not found');
    }
    
    // Set headers for file download
    header('Content-Type: ' . $file['file_type']);
    header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    
    // Output the file
    readfile($filePath);
    
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database error: ' . $e->getMessage());
}
?>