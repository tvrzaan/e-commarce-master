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
 * Display error message
 */
function showGeneralError() {
    if (isset($_SESSION['error'])) {
        $message = htmlspecialchars($_SESSION['error']);
        unset($_SESSION['error']);
        return "<div class='alert alert-danger'>$message</div>";
    }
    return '';
}

// Clear error messages after displaying them
function clearMessages() {
    unset($_SESSION['errors']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
}
?> 