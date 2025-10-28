<?php
include 'db_connect.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($title) || empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO posts (title, content, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $title, $content);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Post added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add post.']);
    }

    $stmt->close();
    $conn->close();
}
?>
