<?php
include 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields required']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $success = $stmt->execute();

    echo json_encode([
        'status' => $success ? 'success' : 'error',
        'message' => $success ? 'Post updated successfully!' : 'Failed to update post.'
    ]);

    $stmt->close();
    $conn->close();
}
?>
