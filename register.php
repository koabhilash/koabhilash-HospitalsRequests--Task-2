<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'editor'); // ✅ capture role from form

    if (empty($username) || empty($password) || empty($role)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all fields.']);
        exit;
    }

    // Password validation
    $errors = [];
    if (strlen($password) < 8) $errors[] = "At least 8 characters.";
    if (!preg_match('/[A-Z]/', $password)) $errors[] = "Add uppercase letter.";
    if (!preg_match('/[a-z]/', $password)) $errors[] = "Add lowercase letter.";
    if (!preg_match('/\d/', $password)) $errors[] = "Add a number.";
    if (!preg_match('/[@$!%*?&]/', $password)) $errors[] = "Add a special symbol.";

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
        exit;
    }

    // Check duplicate username
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists.']);
        exit;
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert with selected role
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed, $role);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! Please login.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error during registration.']);
    }

    $stmt->close();
    $conn->close();
}
?>
