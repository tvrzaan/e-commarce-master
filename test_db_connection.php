<?php
require_once 'config/Database.php';

try {
    // Get database instance
    $database = App\Config\Database::getInstance();
    $connection = $database->getConnection();
    
    // Test the connection with a simple query
    $stmt = $connection->query('SELECT 1');
    
    echo '<div style="color: green; font-weight: bold; padding: 20px;">';
    echo '✓ Database connection successful!<br>';
    echo 'Connection info:<br>';
    echo '- PHP Version: ' . phpversion() . '<br>';
    echo '- PDO Driver: ' . $connection->getAttribute(PDO::ATTR_DRIVER_NAME) . '<br>';
    echo '- Server Version: ' . $connection->getAttribute(PDO::ATTR_SERVER_VERSION) . '<br>';
    echo '</div>';
    
} catch (PDOException $e) {
    echo '<div style="color: red; font-weight: bold; padding: 20px;">';
    echo '✗ Database connection failed!<br>';
    echo 'Error: ' . $e->getMessage() . '<br>';
    echo 'Please check:<br>';
    echo '- Database server is running<br>';
    echo '- Database credentials are correct<br>';
    echo '- Database name exists<br>';
    echo '- Database port is correct<br>';
    echo '</div>';
}
?> 