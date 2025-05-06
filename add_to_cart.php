<?php
session_start();
require_once('autoload.php');
require_once('includes/functions.php');

use App\Models\Cart;

// Set header to return JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['error' => 'not_logged_in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $cartModel = new Cart();
    $userId = $_SESSION['user_id'];
    $productId = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($cartModel->addToCart($userId, $productId, $quantity)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'failed_to_add']);
    }
} else {
    echo json_encode(['error' => 'invalid_request']);
} 