<?php
include 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM posts WHERE id=?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();

    echo json_encode([
        'status' => $success ? 'success' : 'error',
        'message' => $success ? 'Post deleted successfully!' : 'Failed to delete post.'
    ]);

    $stmt->close();
    $conn->close();
}
?>
