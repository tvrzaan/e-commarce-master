<?php
namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

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
        try {
            $columns = $this->db->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN);
            
            $this->hasStockQuantity = in_array('stock_quantity', $columns);
            $this->hasStatus = in_array('status', $columns);
            $this->imageColumn = in_array('image_url', $columns) ? 'image_url' : 'image';
            
        } catch (PDOException $e) {
            // If there's an error, assume basic structure
            error_log("Error checking table structure: " . $e->getMessage());
        }
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

        try {
            $stmt = $this->db->query($sql);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add default values for missing columns
            foreach ($products as &$product) {
                if (!$this->hasStockQuantity) {
                    $product['stock_quantity'] = 0;
                }
                if (!$this->hasStatus) {
                    $product['status'] = 'active';
                }
            }

            return $products;
        } catch (PDOException $e) {
            error_log("Error getting products: " . $e->getMessage());
            return [];
        }
    }

    public function addProduct($data) {
        try {
            $categoryId = $this->getOrCreateCategory($data['category']);

            $columns = ['name', 'description', 'price', 'category_id', $this->imageColumn];
            $values = [':name', ':description', ':price', ':category_id', ':image'];
            $params = [
                ':name' => $data['name'],
                ':description' => $data['description'],
                ':price' => $data['price'],
                ':category_id' => $categoryId,
                ':image' => $data['image']
            ];

            if ($this->hasStockQuantity) {
                $columns[] = 'stock_quantity';
                $values[] = ':stock_quantity';
                $params[':stock_quantity'] = $data['stock_quantity'] ?? 0;
            }

            if ($this->hasStatus) {
                $columns[] = 'status';
                $values[] = ':status';
                $params[':status'] = $data['status'] ?? 'active';
            }

            $sql = "INSERT INTO products (" . implode(', ', $columns) . ") 
                    VALUES (" . implode(', ', $values) . ")";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
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
                  WHERE p.id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                if (!$this->hasStockQuantity) {
                    $product['stock_quantity'] = 0;
                }
                if (!$this->hasStatus) {
                    $product['status'] = 'active';
                }
            }

            return $product;
        } catch (PDOException $e) {
            error_log("Error getting product: " . $e->getMessage());
            return null;
        }
    }

    public function updateProduct($id, $data) {
        try {
            $categoryId = $this->getOrCreateCategory($data['category']);

            $updates = [
                'name = :name',
                'description = :description',
                'price = :price',
                'category_id = :category_id'
            ];

            $params = [
                ':id' => $id,
                ':name' => $data['name'],
                ':description' => $data['description'],
                ':price' => $data['price'],
                ':category_id' => $categoryId
            ];

            if ($this->hasStockQuantity) {
                $updates[] = 'stock_quantity = :stock_quantity';
                $params[':stock_quantity'] = $data['stock_quantity'] ?? 0;
            }

            if ($this->hasStatus) {
                $updates[] = 'status = :status';
                $params[':status'] = $data['status'] ?? 'active';
            }

            if (isset($data['image']) && !empty($data['image'])) {
                $updates[] = $this->imageColumn . ' = :image';
                $params[':image'] = $data['image'];
            }

            $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id) {
        try {
            $product = $this->getProductById($id);
            if ($product && !empty($product['image'])) {
                $imagePath = __DIR__ . '/../' . $product['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }

    private function getOrCreateCategory($categoryName) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM categories WHERE name = :name");
            $stmt->execute([':name' => $categoryName]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($category) {
                return $category['id'];
            }
            
            $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->execute([':name' => $categoryName]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error with category: " . $e->getMessage());
            return null;
        }
    }

    public function getAllCategories() {
        try {
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }
} 