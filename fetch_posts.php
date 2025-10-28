<?php
include 'db_connect.php';
header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM posts";
if (!empty($search)) {
  $searchTerm = "%$search%";
  $sql .= " WHERE title LIKE ? OR content LIKE ?";
}

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if (!empty($search)) {
  $stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
} else {
  $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

$countQuery = "SELECT COUNT(*) as total FROM posts";
if (!empty($search)) $countQuery .= " WHERE title LIKE ? OR content LIKE ?";
$countStmt = $conn->prepare($countQuery);
if (!empty($search)) {
  $countStmt->bind_param("ss", $searchTerm, $searchTerm);
}
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];

echo json_encode(['posts' => $posts, 'total' => $total]);
?>
