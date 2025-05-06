<?php
use App\Models\Cart;

// Get cart count if user is logged in
$cartCount = 0;
if (isLoggedIn()) {
    $cartModel = new Cart();
    $cartItems = $cartModel->getCartItems($_SESSION['user_id']);
    $cartCount = count($cartItems);
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">E-Commerce Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                       href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" 
                       href="products.php">Products</a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'admin/') !== false ? 'active' : ''; ?>" 
                       href="admin/dashboard.php">Dashboard</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>" 
                           href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if ($cartCount > 0): ?>
                                <span class="badge bg-danger"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="admin/dashboard.php">Admin Dashboard</a></li>
                                <li><a class="dropdown-item" href="admin/orders.php">Orders</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" 
                           href="views/auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>" 
                           href="views/auth/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav> 