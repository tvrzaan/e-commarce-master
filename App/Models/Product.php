<?php
namespace App\Models;

use App\Config\Database;
use mysqli;

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->checkTableStructure();
    }

    private function checkTableStructure() {
        // Check if products table exists
        $sql = "SHOW TABLES LIKE 'products'";
        $result = mysqli_query($this->db, $sql);
        
        if (mysqli_num_rows($result) == 0) {
            // Create products table if it doesn't exist
            $sql = "CREATE TABLE products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                category VARCHAR(100),
                image_url VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            mysqli_query($this->db, $sql);
        }
    }

    public function getAllProducts() {
        // Simple query to get all products
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $result = mysqli_query($this->db, $sql);
        
        // Check if query was successful
        if (!$result) {
            return [];
        }
        
        // Get all products
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        return $products;
    }

    public function addProduct($data) {
        // Escape special characters to prevent SQL injection
        $name = mysqli_real_escape_string($this->db, $data['name']);
        $description = mysqli_real_escape_string($this->db, $data['description']);
        $price = floatval($data['price']);
        $category = mysqli_real_escape_string($this->db, $data['category']);
        $image = mysqli_real_escape_string($this->db, $data['image']);

        // Simple insert query
        $sql = "INSERT INTO products (name, description, price, category, image_url) 
                VALUES ('$name', '$description', $price, '$category', '$image')";
        
        return mysqli_query($this->db, $sql);
    }

    public function getProductById($id) {
        // Simple query to get a single product
        $sql = "SELECT * FROM products WHERE id = '$id'";
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return null;
        }
        
        return mysqli_fetch_assoc($result);
    }

    public function deleteProduct($id) {
        // Simple delete query
        $sql = "DELETE FROM products WHERE id = '$id'";
        return mysqli_query($this->db, $sql);
    }

    public function updateProduct($id, $data) {
        // Escape special characters
        $name = mysqli_real_escape_string($this->db, $data['name']);
        $description = mysqli_real_escape_string($this->db, $data['description']);
        $price = floatval($data['price']);
        $category = mysqli_real_escape_string($this->db, $data['category']);

        // Start building the update query
        $sql = "UPDATE products SET 
                name = '$name',
                description = '$description',
                price = $price,
                category = '$category'";

        // Add image update if provided
        if (isset($data['image']) && !empty($data['image'])) {
            $image = mysqli_real_escape_string($this->db, $data['image']);
            $sql .= ", image_url = '$image'";
        }

        // Complete the query
        $sql .= " WHERE id = '$id'";
        
        return mysqli_query($this->db, $sql);
    }
} 