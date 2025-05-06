<?php
session_start();
require_once('autoload.php');
require_once('includes/functions.php');

use App\Models\User;

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !hash_equals(hash_hmac('sha256', session_id(), 'secret_key'), $_POST['csrf_token'])) {
    $_SESSION['errors'] = ['Invalid form submission.'];
    header('Location: views/auth/register.php');
    exit();
}

$errors = [];

// Validate required fields
$required_fields = ['firstName', 'lastname', 'email', 'password', 'confirmpassword', 'birthday', 'gender', 'phoneNumber', 'address'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[$field] = ucfirst($field) . ' is required';
    }
}

// Validate email format
if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format';
}

// Validate password
if (!empty($_POST['password'])) {
    $password = $_POST['password'];
    $errors = [];
    
    // Check minimum length
    if (strlen($password) < 8) {
        $errors['password'][] = 'Password must be at least 8 characters long';
    }
    
    // Check for uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        $errors['password'][] = 'Password must contain at least one uppercase letter';
    }
    
    // Check for lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        $errors['password'][] = 'Password must contain at least one lowercase letter';
    }
    
    // Check for number
    if (!preg_match('/[0-9]/', $password)) {
        $errors['password'][] = 'Password must contain at least one number';
    }
    
    // If any password requirements failed
    if (!empty($errors['password'])) {
        $errors['password'] = implode(', ', $errors['password']);
    }
}

// Validate password confirmation
if (!empty($_POST['password']) && !empty($_POST['confirmpassword'])) {
    if ($_POST['password'] !== $_POST['confirmpassword']) {
        $errors['confirmpassword'] = 'Passwords do not match';
    }
}

// Validate phone number
if (!empty($_POST['phoneNumber']) && !preg_match('/^[0-9]{10,15}$/', $_POST['phoneNumber'])) {
    $errors['phoneNumber'] = 'Invalid phone number format';
}

// Validate birthday (must be at least 13 years old)
if (!empty($_POST['birthday'])) {
    $birthday = new DateTime($_POST['birthday']);
    $today = new DateTime();
    $age = $today->diff($birthday)->y;
    if ($age < 13) {
        $errors['birthday'] = 'You must be at least 13 years old to register';
    }
}

// If there are validation errors, redirect back to the registration form
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: views/auth/register.php');
    exit();
}

try {
    $userModel = new User();

    // Check if email already exists
    if ($userModel->isEmailTaken($_POST['email'])) {
        $_SESSION['errors'] = ['email' => 'This email is already registered'];
        header('Location: views/auth/register.php');
        exit();
    }

    // Prepare user data
    $userData = [
        'username' => strtolower($_POST['firstName'] . '.' . $_POST['lastname']),
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'email' => $_POST['email'],
        'full_name' => $_POST['firstName'] . ' ' . $_POST['lastname'],
        'phone' => $_POST['phoneNumber'],
        'address' => $_POST['address'],
        'birthday' => $_POST['birthday'],
        'gender' => $_POST['gender']
    ];

    // Create new user
    if ($userModel->createUser($userData)) {
        $_SESSION['success_message'] = 'Registration successful! Please log in.';
        header('Location: views/auth/login.php');
        exit();
    } else {
        throw new Exception('Failed to create user');
    }
} catch (Exception $e) {
    $_SESSION['errors'] = ['An error occurred during registration. Please try again.'];
    header('Location: views/auth/register.php');
    exit();
}
?> 