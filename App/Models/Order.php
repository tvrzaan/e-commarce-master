<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllOrders() {
        $sql = "SELECT o.*, u.username, u.email, u.full_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($orderId) {
        // Get order details
        $sql = "SELECT o.*, u.username, u.email, u.full_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        // Get order items
        $sql = "SELECT oi.*, p.name as product_name, p.image_url as image,
                oi.price_per_unit as price,
                (oi.price_per_unit * oi.quantity) as total_price
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    public function updateOrderStatus($orderId, $status) {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $orderId,
            ':status' => $status
        ]);
    }

    public function getOrdersByUserId($userId) {
        $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count 
                FROM orders o 
                WHERE o.user_id = :user_id 
                ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderStatistics() {
        $stats = [];
        
        // Total orders
        $sql = "SELECT COUNT(*) as total FROM orders";
        $stmt = $this->db->query($sql);
        $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Orders by status
        $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $stmt = $this->db->query($sql);
        $stats['orders_by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total revenue
        $sql = "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'";
        $stmt = $this->db->query($sql);
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }
} 