<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ecommerce_db");
    echo "✓ Database 'ecommerce_db' created or already exists<br>";
    
    // Select the database
    $pdo->exec("USE ecommerce_db");
    
    // Read and execute the SQL file
    $sql = file_get_contents('DATABASE.sql');
    $pdo->exec($sql);
    
    echo "✓ Database tables created successfully<br>";
    echo '<a href="test_db_connection.php">Test Database Connection</a>';
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?> 