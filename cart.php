<?php
session_start();
require_once('autoload.php');
require_once('includes/functions.php');

use App\Models\Cart;

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: views/auth/login.php');
    exit();
}

$cartModel = new Cart();
$cartItems = $cartModel->getCartItems($_SESSION['user_id']);
$cartTotal = $cartModel->getCartTotal($_SESSION['user_id']);

// Handle quantity updates
if (isset($_POST['update_quantity'])) {
    $cartId = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    if ($quantity > 0) {
        $cartModel->updateQuantity($cartId, $quantity);
    }
    header('Location: cart.php');
    exit();
}

// Handle item removal
if (isset($_GET['remove'])) {
    $cartId = $_GET['remove'];
    $cartModel->removeFromCart($cartId, $_SESSION['user_id']);
    header('Location: cart.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Main Content -->
    <div class="container my-4">
        <h1 class="mb-4">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="products.php">Continue shopping</a>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             style="width: 50px; height: 50px; object-fit: contain; margin-right: 10px;">
                                        <span><?php echo htmlspecialchars($item['name']); ?></span>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <form method="post" class="d-flex align-items-center" style="max-width: 150px;">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" class="form-control form-control-sm me-2">
                                        <button type="submit" name="update_quantity" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                                <td>
                                    <a href="cart.php?remove=<?php echo $item['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to remove this item?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td><strong>$<?php echo number_format($cartTotal, 2); ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <a href="products.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                    <a href="checkout.php" class="btn btn-primary">
                        Proceed to Checkout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 