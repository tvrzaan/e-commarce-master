<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecommerce_db';

try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        throw new Exception("Database connection failed. Please try again later.");
    }

    // Set charset to utf8
    if (!$conn->set_charset("utf8")) {
        error_log("Error setting charset: " . $conn->error);
    }

    // Test if database exists
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
    if ($result->num_rows === 0) {
        error_log("Database '$database' does not exist");
        throw new Exception("Database configuration error");
    }

    // Test if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows === 0) {
        error_log("Table 'users' does not exist");
        throw new Exception("Database configuration error");
    }

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("A database error occurred. Please try again later.");
}
?>
