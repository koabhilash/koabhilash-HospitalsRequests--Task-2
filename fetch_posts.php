<?php
include 'db_connect.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
echo json_encode($posts);
$conn->close();
?>
