<?php
session_start();
require_once('../autoload.php');
require_once('../includes/functions.php');

use App\Models\Order;

// Check if user is admin
if (!is_admin()) {
    header('Location: ../index.php');
    exit();
}

$orderModel = new Order();
$message = '';
$error = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];
    
    if ($orderModel->updateOrderStatus($orderId, $newStatus)) {
        $message = "Order status updated successfully!";
    } else {
        $error = "Failed to update order status!";
    }
}

// Get order details if ID is provided
$orderDetails = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $orderDetails = $orderModel->getOrderById($_GET['id']);
    if (!$orderDetails) {
        header('Location: orders.php');
        exit();
    }
}

// Get all orders and statistics
$orders = $orderModel->getAllOrders();
$stats = $orderModel->getOrderStatistics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Main Content -->
    <div class="container my-4">
        <h1 class="mb-4">Order Management</h1>

        <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <h2><?php echo number_format($stats['total_orders']); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h2>$<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Orders by Status</h5>
                        <div class="row">
                            <?php foreach ($stats['orders_by_status'] as $status): ?>
                            <div class="col-4 text-center">
                                <h6><?php echo ucfirst($status['status']); ?></h6>
                                <h3><?php echo $status['count']; ?></h3>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($orderDetails): ?>
        <!-- Order Details -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order #<?php echo $orderDetails['id']; ?> Details</h5>
                <a href="orders.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($orderDetails['full_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($orderDetails['email']); ?></p>
                        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($orderDetails['shipping_address']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($orderDetails['created_at'])); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($orderDetails['payment_method']); ?></p>
                        <p><strong>Total Amount:</strong> $<?php echo number_format($orderDetails['total_amount'], 2); ?></p>
                        <form method="post" class="d-flex align-items-center">
                            <input type="hidden" name="order_id" value="<?php echo $orderDetails['id']; ?>">
                            <select name="status" class="form-select w-auto me-2">
                                <?php
                                $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                                foreach ($statuses as $status):
                                ?>
                                <option value="<?php echo $status; ?>" 
                                        <?php echo $orderDetails['status'] === $status ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($status); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary">
                                Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <h6>Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderDetails['items'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo '../' . htmlspecialchars($item['image']); ?>" 
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
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Orders List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <div>
                                        <?php echo htmlspecialchars($order['full_name']); ?>
                                        <small class="text-muted d-block"><?php echo htmlspecialchars($order['email']); ?></small>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo match($order['status']) {
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="orders.php?id=<?php echo $order['id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 