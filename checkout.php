<?php
session_start();
require_once('autoload.php');
require_once('includes/functions.php');

use App\Models\Cart;
use App\Models\Order;
use App\Config\Database;

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: views/auth/login.php');
    exit();
}

// Initialize models
$cartModel = new Cart();
$orderModel = new Order();

// Get cart items
$userId = $_SESSION['user_id'];
$cartItems = $cartModel->getCartItems($userId);
$cartTotal = $cartModel->getCartTotal($userId);

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
    if (empty($_POST['shipping_address']) || empty($_POST['payment_method'])) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            // Start transaction
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            // Create order
            $orderData = [
                'user_id' => $userId,
                'total_amount' => $cartTotal,
                'shipping_address' => $_POST['shipping_address'],
                'payment_method' => $_POST['payment_method'],
                'status' => 'pending'
            ];

            // Insert order and get order ID
            $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, status) 
                    VALUES (:user_id, :total_amount, :shipping_address, :payment_method, :status)";
            $stmt = $db->prepare($sql);
            $stmt->execute($orderData);
            $orderId = $db->lastInsertId();

            // Insert order items
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES (:order_id, :product_id, :quantity, :price)";
            $stmt = $db->prepare($sql);

            foreach ($cartItems as $item) {
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }

            // Clear the cart
            $cartModel->clearCart($userId);

            // Commit transaction
            $db->commit();

            // Set success message and redirect to home page
            $_SESSION['success_message'] = 'Order placed successfully! You can check your order status in your profile.';
            header('Location: index.php');
            exit();

        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();
            $error = 'An error occurred while processing your order. Please try again.';
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

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Choose...</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="cash_on_delivery">Cash on Delivery</option>
                                </select>
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