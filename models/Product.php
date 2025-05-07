<?php
namespace App\Models;

use App\Config\Database;

class Product {
    private $db;
    private $hasStockQuantity = false;
    private $hasStatus = false;
    private $imageColumn = 'image';

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
                category_id INT,
                image_url VARCHAR(255),
                stock_quantity INT DEFAULT 0,
                status VARCHAR(20) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            mysqli_query($this->db, $sql);
        }

        // Check columns
        $sql = "SHOW COLUMNS FROM products";
        $result = mysqli_query($this->db, $sql);
        $columns = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row['Field'];
        }
        
        $this->hasStockQuantity = in_array('stock_quantity', $columns);
        $this->hasStatus = in_array('status', $columns);
        $this->imageColumn = in_array('image_url', $columns) ? 'image_url' : 'image';
    }

    public function getAllProducts() {
        // Start with basic columns that we know exist
        $sql = "SELECT p.id, p.name, p.description, p.price, 
                p.category_id, p.{$this->imageColumn} as image,
                c.name as category_name";

        // Add optional columns if they exist
        if ($this->hasStockQuantity) {
            $sql .= ", p.stock_quantity";
        }
        if ($this->hasStatus) {
            $sql .= ", p.status";
        }

        $sql .= " FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  ORDER BY p.id DESC";

        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return [];
        }

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Add default values for missing columns
            if (!$this->hasStockQuantity) {
                $row['stock_quantity'] = 0;
            }
            if (!$this->hasStatus) {
                $row['status'] = 'active';
            }
            $products[] = $row;
        }

        return $products;
    }

    public function addProduct($data) {
        $categoryId = $this->getOrCreateCategory($data['category']);

        // Escape special characters
        $name = mysqli_real_escape_string($this->db, $data['name']);
        $description = mysqli_real_escape_string($this->db, $data['description']);
        $price = floatval($data['price']);
        $image = mysqli_real_escape_string($this->db, $data['image']);

        // Build the query
        $columns = ['name', 'description', 'price', 'category_id', $this->imageColumn];
        $values = ["'$name'", "'$description'", $price, $categoryId, "'$image'"];

        if ($this->hasStockQuantity) {
            $stockQuantity = intval($data['stock_quantity'] ?? 0);
            $columns[] = 'stock_quantity';
            $values[] = $stockQuantity;
        }

        if ($this->hasStatus) {
            $status = mysqli_real_escape_string($this->db, $data['status'] ?? 'active');
            $columns[] = 'status';
            $values[] = "'$status'";
        }

        $sql = "INSERT INTO products (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $values) . ")";

        return mysqli_query($this->db, $sql);
    }

    public function getProductById($id) {
        // Start with basic columns
        $sql = "SELECT p.id, p.name, p.description, p.price, 
                p.category_id, p.{$this->imageColumn} as image,
                c.name as category_name";

        // Add optional columns if they exist
        if ($this->hasStockQuantity) {
            $sql .= ", p.stock_quantity";
        }
        if ($this->hasStatus) {
            $sql .= ", p.status";
        }

        $sql .= " FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = '$id'";

        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return null;
        }

        $product = mysqli_fetch_assoc($result);
        if ($product) {
            if (!$this->hasStockQuantity) {
                $product['stock_quantity'] = 0;
            }
            if (!$this->hasStatus) {
                $product['status'] = 'active';
            }
        }

        return $product;
    }

    public function updateProduct($id, $data) {
        $categoryId = $this->getOrCreateCategory($data['category']);

        // Escape special characters
        $name = mysqli_real_escape_string($this->db, $data['name']);
        $description = mysqli_real_escape_string($this->db, $data['description']);
        $price = floatval($data['price']);

        // Build the update query
        $updates = [
            "name = '$name'",
            "description = '$description'",
            "price = $price",
            "category_id = $categoryId"
        ];

        if ($this->hasStockQuantity) {
            $stockQuantity = intval($data['stock_quantity'] ?? 0);
            $updates[] = "stock_quantity = $stockQuantity";
        }

        if ($this->hasStatus) {
            $status = mysqli_real_escape_string($this->db, $data['status'] ?? 'active');
            $updates[] = "status = '$status'";
        }

        if (isset($data['image']) && !empty($data['image'])) {
            $image = mysqli_real_escape_string($this->db, $data['image']);
            $updates[] = "{$this->imageColumn} = '$image'";
        }

        $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = '$id'";
        return mysqli_query($this->db, $sql);
    }

    public function deleteProduct($id) {
        $product = $this->getProductById($id);
        if ($product && !empty($product['image'])) {
            $imagePath = __DIR__ . '/../' . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $sql = "DELETE FROM products WHERE id = '$id'";
        return mysqli_query($this->db, $sql);
    }

    private function getOrCreateCategory($categoryName) {
        // Escape category name
        $categoryName = mysqli_real_escape_string($this->db, $categoryName);
        
        // Check if category exists
        $sql = "SELECT id FROM categories WHERE name = '$categoryName'";
        $result = mysqli_query($this->db, $sql);
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            return $row['id'];
        }
        
        // Create new category
        $sql = "INSERT INTO categories (name) VALUES ('$categoryName')";
        mysqli_query($this->db, $sql);
        
        return mysqli_insert_id($this->db);
    }

    public function getAllCategories() {
        $sql = "SELECT * FROM categories ORDER BY name";
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return [];
        }
        
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        
        return $categories;
    }
} 