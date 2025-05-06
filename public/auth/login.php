<?php
session_start();
require_once(__DIR__ . '/../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/auth/login.php');
    exit;
}

// Validate input
if (empty($_POST['email']) || empty($_POST['password'])) {
    $_SESSION['error'] = "Email and password are required";
    header('Location: ../../views/auth/login.php');
    exit;
}

try {
    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Invalid email or password";
        header('Location: ../../views/auth/login.php');
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($_POST['password'], $user['password'])) {
        // Password is correct, set up session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        
        // Redirect to home page
        header('Location: ../../index.php');
        exit;
    } else {
        $_SESSION['error'] = "Invalid email or password";
        header('Location: ../../views/auth/login.php');
        exit;
    }
} catch (\Throwable $th) {
    $_SESSION['error'] = "Login failed: " . $th->getMessage();
    header('Location: ../../views/auth/login.php');
    exit;
}
?> 