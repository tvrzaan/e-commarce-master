<?php
/**
 * Display error message for a specific field
 */
function showError($field) {
    if (isset($_SESSION['errors']) && isset($_SESSION['errors'][$field])) {
        $error = htmlspecialchars($_SESSION['errors'][$field]);
        return "<div class='text-danger small mt-1'>$error</div>";
    }
    return '';
}

/**
 * Display success message
 */
function showSuccess() {
    if (isset($_SESSION['success'])) {
        $message = htmlspecialchars($_SESSION['success']);
        unset($_SESSION['success']);
        return "<div class='alert alert-success'>$message</div>";
    }
    return '';
}

/**
 * Display general error message
 */
function showGeneralError() {
    if (isset($_SESSION['error'])) {
        $message = htmlspecialchars($_SESSION['error']);
        unset($_SESSION['error']);
        return "<div class='alert alert-danger'>$message</div>";
    }
    return '';
}

/**
 * Clear all session messages
 */
function clearMessages() {
    unset($_SESSION['errors']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please login to access this page";
        header('Location: /e-commarce-master/views/auth/login.php');
        exit;
    }
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];
    }
    return null;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

function isAdmin() {
    return is_admin();
}
?>