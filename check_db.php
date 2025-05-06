<?php
require_once('includes/db.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Test connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get table structure
    $result = $conn->query("SHOW CREATE TABLE users");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Table structure:\n";
        print_r($row);
    } else {
        echo "Error getting table structure: " . $conn->error;
    }
    
    // Test a sample query
    $stmt = $conn->prepare("SELECT id, username, email, password FROM users LIMIT 1");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "\n\nNumber of users: " . $result->num_rows;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 