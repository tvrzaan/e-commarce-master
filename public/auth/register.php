<?php
session_start();
require_once(__DIR__ . '/../../includes/db.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/auth/register.php');
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header('Location: ../../views/auth/register.php');
    exit;
}

// Clear the used CSRF token
unset($_SESSION['csrf_token']);

// Validate input
$required_fields = ['username', 'email', 'password', 'confirm_password'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "All fields are required";
        header('Location: ../../views/auth/register.php');
        exit;
    }
}

// Validate email format
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format";
    header('Location: ../../views/auth/register.php');
    exit;
}

// Validate password match
if ($_POST['password'] !== $_POST['confirm_password']) {
    $_SESSION['error'] = "Passwords do not match";
    header('Location: ../../views/auth/register.php');
    exit;
}

// Validate password strength
if (strlen($_POST['password']) < 8) {
    $_SESSION['error'] = "Password must be at least 8 characters long";
    header('Location: ../../views/auth/register.php');
    exit;
}

try {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already registered";
        header('Location: ../../views/auth/register.php');
        exit;
    }
    
    // Hash password
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['username'], $_POST['email'], $password_hash);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! Please login.";
        header('Location: ../../views/auth/login.php');
        exit;
    } else {
        throw new Exception("Registration failed");
    }
} catch (\Throwable $th) {
    error_log("Registration error: " . $th->getMessage());
    $_SESSION['error'] = "Registration failed. Please try again later.";
    header('Location: ../../views/auth/register.php');
    exit;
}
?> 