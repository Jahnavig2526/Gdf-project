<?php
include 'db.php';

if (isset($_GET['domain'])) {
    $domain = $_GET['domain'];

    $stmt = $conn->prepare("SELECT * FROM files WHERE domain = ?");
    $stmt->bind_param("s", $domain);
    $stmt->execute();
    $result = $stmt->get_result();

    $files = [];
    while ($row = $result->fetch_assoc()) {
        $files[] = $row;
    }

    echo json_encode($files);
}
?>
