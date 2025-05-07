<?php
session_start();
require_once('autoload.php');
require_once('includes/functions.php');

use App\Models\Order;

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: views/auth/login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: profile.php');
    exit();
}

$orderId = $_GET['id'];
$orderModel = new Order();
$order = $orderModel->getOrderById($orderId);

// Check if order exists and belongs to the current user
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: profile.php');
    exit();
}

// Get status badge color
function getStatusBadgeColor($status) {
    return match($status) {
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        default => 'secondary'
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Details #<?php echo $orderId; ?> - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Main Content -->
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Order Details #<?php echo $orderId; ?></h1>
            <a href="profile.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>

        <!-- Order Information -->
        <div class="row">
            <div class="col-md-8">
                <!-- Order Items -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                     style="width: 50px; height: 50px; object-fit: contain; margin-right: 10px;">
                                                <?php echo htmlspecialchars($item['product_name']); ?>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                        <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Order Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Order Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <span class="badge bg-<?php echo getStatusBadgeColor($order['status']); ?> ms-2">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Order Date:</strong><br>
                            <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?>
                        </div>
                        <?php if ($order['created_at'] !== $order['updated_at']): ?>
                        <div class="mb-3">
                            <strong>Last Updated:</strong><br>
                            <?php echo date('F j, Y g:i A', strtotime($order['updated_at'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Shipping Address:</strong><br>
                            <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                        </div>

                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                        <?php if (isset($order['phone'])): ?>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 