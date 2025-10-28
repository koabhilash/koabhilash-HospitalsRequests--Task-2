<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // --- Validate empty fields ---
    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all fields.']);
        exit;
    }

    // --- Password validation ---
    $errors = [];
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters long.";
    if (!preg_match('/[A-Z]/', $password)) $errors[] = "Include at least one uppercase letter.";
    if (!preg_match('/[a-z]/', $password)) $errors[] = "Include at least one lowercase letter.";
    if (!preg_match('/\d/', $password)) $errors[] = "Include at least one number.";
    if (!preg_match('/[@$!%*?&]/', $password)) $errors[] = "Include at least one special symbol (@, $, !, %, *, ?, &).";

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
        exit;
    }

    // --- Check for duplicate username ---
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists! Choose a different one.']);
        exit;
    }

    // --- Secure password hashing ---
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // --- Insert new user ---
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);

    if ($stmt->execute()) {
        // Optionally start a session for auto-login
        $_SESSION['username'] = $username;
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! Please login.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error during registration. Try again.']);
    }

    $stmt->close();
    $conn->close();
}
?>

