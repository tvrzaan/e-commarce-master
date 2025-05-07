<?php
session_start();
require_once('autoload.php');
require_once('includes/functions.php');

use App\Models\Cart;
use App\Models\Order;
use App\Config\Database;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: views/auth/login.php');
    exit();
}

// Initialize models
try {
    $cartModel = new Cart();
    $orderModel = new Order();
} catch (Exception $e) {
    error_log("Error initializing models: " . $e->getMessage());
    die("System error: Unable to process checkout. Please try again later.");
}

// Get cart items
$userId = $_SESSION['user_id'];
try {
    $cartItems = $cartModel->getCartItems($userId);
    $cartTotal = $cartModel->getCartTotal($userId);
} catch (Exception $e) {
    error_log("Error getting cart items: " . $e->getMessage());
    die("System error: Unable to retrieve cart items. Please try again later.");
}

// Redirect if cart is empty
if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (empty($_POST['shipping_address'])) {
        $error = 'Please provide a shipping address.';
    } else {
        try {
            // Start transaction
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            error_log("Starting order process for user: " . $userId);
            error_log("Cart total: " . $cartTotal);

            // Create order
            $orderData = [
                'user_id' => $userId,
                'total_amount' => $cartTotal,
                'shipping_address' => $_POST['shipping_address'],
                'status' => 'pending',
                'payment_status' => 'pending'
            ];

            // Insert order and get order ID
            $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, status, payment_status) 
                    VALUES (:user_id, :total_amount, :shipping_address, :status, :payment_status)";
            $stmt = $db->prepare($sql);
            
            error_log("Executing order insert with data: " . print_r($orderData, true));
            
            if (!$stmt->execute($orderData)) {
                throw new Exception("Failed to create order: " . implode(", ", $stmt->errorInfo()));
            }
            
            $orderId = $db->lastInsertId();
            error_log("Order created with ID: " . $orderId);

            // Insert order items
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price_per_unit, subtotal) 
                    VALUES (:order_id, :product_id, :quantity, :price_per_unit, :subtotal)";
            $stmt = $db->prepare($sql);

            foreach ($cartItems as $item) {
                $itemData = [
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price_per_unit' => $item['price'],
                    ':subtotal' => $item['price'] * $item['quantity']
                ];
                
                error_log("Inserting order item: " . print_r($itemData, true));
                
                if (!$stmt->execute($itemData)) {
                    throw new Exception("Failed to create order item: " . implode(", ", $stmt->errorInfo()));
                }
            }

            // Clear the cart
            if (!$cartModel->clearCart($userId)) {
                throw new Exception("Failed to clear cart after order creation");
            }

            // Commit transaction
            $db->commit();
            error_log("Order process completed successfully for order ID: " . $orderId);

            // Set success message and redirect to home page
            $_SESSION['success_message'] = 'Order placed successfully! You can check your order status in your profile.';
            header('Location: index.php');
            exit();

        } catch (Exception $e) {
            // Rollback transaction on error
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("Order processing error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $error = 'An error occurred while processing your order. Please try again. Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Main Content -->
    <div class="container my-4">
        <h1 class="mb-4">Checkout</h1>

        <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Order Summary -->
            <div class="col-md-4 order-md-2 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cartItems as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span class="text-muted">$<?php echo number_format($item['total_price'], 2); ?></span>
                            </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong>$<?php echo number_format($cartTotal, 2); ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Checkout Form -->
            <div class="col-md-8 order-md-1">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Shipping Information</h4>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">Shipping Address</label>
                                <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required></textarea>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between">
                                <a href="cart.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Cart
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Place Order <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 