<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>ElectroShop</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <header>
    <h1><a href="/index.php">ElectroShop</a></h1>
    <nav>
      <a href="/products.php">Products</a>
      <a href="/cart.php">Cart</a>
      <?php if (is_logged_in()): ?>
        <a href="/logout.php">Logout</a>
      <?php else: ?>
        <a href="/login.php">Login</a>
        <a href="/register.php">Register</a>
      <?php endif; ?>
    </nav>
  </header>