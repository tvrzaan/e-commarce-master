<?php
session_start();
require_once(__DIR__ . '/../../includes/db.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug information
$_SESSION['debug'] = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/auth/login.php');
    exit;
}

// Log POST data (remove in production)


// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['debug'] .= "CSRF validation failed\n";
    $_SESSION['error'] = "Invalid form submission";
    header('Location: ../../views/auth/login.php');
    exit;
}

// Clear the used CSRF token
unset($_SESSION['csrf_token']);

// Validate input
if (empty($_POST['email']) || empty($_POST['password'])) {

    $_SESSION['error'] = "Email and password are required";
    header('Location: ../../views/auth/login.php');
    exit;
}

// Validate email format
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

    header('Location: ../../views/auth/login.php');
    exit;
}

try {
    $_SESSION['debug'] .= "Attempting database connection\n";
    
    // Test database connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    $_SESSION['debug'] .= "Database connected successfully\n";
    
    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $_SESSION['debug'] .= "SQL prepared successfully\n";
    
    $stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $_SESSION['debug'] .= "Query executed. Found rows: " . $result->num_rows . "\n";
    
    if ($result->num_rows === 0) {

        header('Location: ../../views/auth/login.php');
        exit;
    }
    
    $user = $result->fetch_assoc();

    
    // Verify password
    if (password_verify($_POST['password'], $user['password'])) {
        $_SESSION['debug'] .= "Password verified successfully\n";
        
        // Password is correct, set up session
        session_regenerate_id(true); // Prevent session fixation
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        
        // Set admin flag if applicable
        if (isset($user['role']) && $user['role'] === 'admin') {
            $_SESSION['is_admin'] = true;
        }

        // Clear sensitive data
        unset($user['password']);
        
        // Regenerate CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        $_SESSION['debug'] .= "Login successful, redirecting user\n";
        
        // Redirect based on role
        if (isset($user['role']) && $user['role'] === 'admin') {
            header('Location: ../../admin/dashboard.php');
        } else {
            header('Location: ../../index.php');
        }
        exit;
    } else {
      
        header('Location: ../../views/auth/login.php');
        exit;
    }
} catch (\Throwable $th) {
    error_log("Login error details: " . $th->getMessage());
    error_log("Stack trace: " . $th->getTraceAsString());
    $_SESSION['debug'] .= "Error occurred: " . $th->getMessage() . "\n";
    $_SESSION['error'] = "Login failed. Please try again later.";
    header('Location: ../../views/auth/login.php');
    exit;
}
?> 