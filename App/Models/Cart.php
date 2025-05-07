<?php
namespace App\Models;

use App\Config\Database;
use mysqli;

class Cart {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        // First check if product already exists in cart
        $sql = "SELECT id, quantity FROM cart WHERE user_id = '$userId' AND product_id = '$productId'";
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return false;
        }
        
        $existingItem = mysqli_fetch_assoc($result);

        if ($existingItem) {
            // Update quantity if product exists
            $newQuantity = $existingItem['quantity'] + $quantity;
            $sql = "UPDATE cart SET quantity = '$newQuantity' WHERE id = '{$existingItem['id']}'";
            return mysqli_query($this->db, $sql);
        } else {
            // Insert new item if product doesn't exist
            $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$userId', '$productId', '$quantity')";
            return mysqli_query($this->db, $sql);
        }
    }

    public function getCartItems($userId) {
        // Get all items in user's cart with product details
        $sql = "SELECT c.*, p.name, p.price, p.image_url as image, (p.price * c.quantity) as total_price 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = '$userId'";
        
        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return [];
        }
        
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        return $items;
    }

    public function updateQuantity($cartId, $quantity) {
        // Simple update query
        $sql = "UPDATE cart SET quantity = '$quantity' WHERE id = '$cartId'";
        return mysqli_query($this->db, $sql);
    }

    public function removeFromCart($cartId, $userId) {
        // Simple delete query
        $sql = "DELETE FROM cart WHERE id = '$cartId' AND user_id = '$userId'";
        return mysqli_query($this->db, $sql);
    }

    public function getCartTotal($userId) {
        $sql = "SELECT SUM(p.price * c.quantity) as total 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = '$userId'";
        
        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return 0;
        }
        
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    public function clearCart($userId) {
        $sql = "DELETE FROM cart WHERE user_id = '$userId'";
        return mysqli_query($this->db, $sql);
    }
} 