<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Cart {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        // Check if product already exists in cart
        $stmt = $this->db->prepare("SELECT id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Update quantity if product exists
            $newQuantity = $existingItem['quantity'] + $quantity;
            $stmt = $this->db->prepare("UPDATE cart SET quantity = :quantity WHERE id = :id");
            return $stmt->execute([':quantity' => $newQuantity, ':id' => $existingItem['id']]);
        } else {
            // Insert new item if product doesn't exist
            $stmt = $this->db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
            return $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId,
                ':quantity' => $quantity
            ]);
        }
    }

    public function getCartItems($userId) {
        $stmt = $this->db->prepare("
            SELECT c.*, p.name, p.price, p.image_url as image, (p.price * c.quantity) as total_price 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantity($cartId, $quantity) {
        $stmt = $this->db->prepare("UPDATE cart SET quantity = :quantity WHERE id = :id");
        return $stmt->execute([':quantity' => $quantity, ':id' => $cartId]);
    }

    public function removeFromCart($cartId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([':id' => $cartId, ':user_id' => $userId]);
    }

    public function getCartTotal($userId) {
        $stmt = $this->db->prepare("
            SELECT SUM(p.price * c.quantity) as total 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function clearCart($userId) {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = :user_id");
        return $stmt->execute([':user_id' => $userId]);
    }
} 