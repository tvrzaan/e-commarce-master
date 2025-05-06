<?php
session_start();
require_once('autoload.php');
require_once('includes/functions.php');

use App\Models\User;
use App\Models\Order;

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: views/auth/login.php');
    exit();
}

$userModel = new User();
$orderModel = new Order();
$user = $userModel->getUserById($_SESSION['user_id']);
$userOrders = $orderModel->getOrdersByUserId($_SESSION['user_id']);
$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $userData = [
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address']
        ];

        // Check if email is already taken by another user
        if ($userModel->isEmailTaken($_POST['email'], $_SESSION['user_id'])) {
            $error = "This email is already taken by another user.";
        } else {
            if ($userModel->updateUser($_SESSION['user_id'], $userData)) {
                $message = "Profile updated successfully!";
                // Refresh user data
                $user = $userModel->getUserById($_SESSION['user_id']);
            } else {
                $error = "Failed to update profile.";
            }
        }
    } 
    // Handle password update
    elseif (isset($_POST['update_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            $error = "New passwords do not match.";
        } elseif (strlen($newPassword) < 6) {
            $error = "New password must be at least 6 characters long.";
        } else {
            if ($userModel->updatePassword($_SESSION['user_id'], $currentPassword, $newPassword)) {
                $message = "Password updated successfully!";
            } else {
                $error = "Current password is incorrect.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Main Content -->
    <div class="container my-4">
        <h1 class="mb-4">My Profile</h1>

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

        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                <small class="text-muted">Username cannot be changed</small>
                            </div>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" 
                                          rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" 
                                       name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-primary">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Member Since:</strong> 
                            <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                        </p>
                        <p><strong>Account Status:</strong> 
                            <span class="badge bg-success">Active</span>
                        </p>
                        <?php if (isAdmin()): ?>
                        <p><strong>Account Type:</strong> 
                            <span class="badge bg-primary">Administrator</span>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">My Orders</h5>
            </div>
            <div class="card-body">
                <?php if (empty($userOrders)): ?>
                <p class="text-muted">You haven't placed any orders yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Items</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userOrders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
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
                                <td>
                                    <?php echo $order['item_count']; ?> item(s)
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 