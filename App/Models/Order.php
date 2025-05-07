<?php
namespace App\Models;

use App\Config\Database;
use mysqli;

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllOrders() {
        // Simple query to get all orders with user information
        $sql = "SELECT o.*, u.username, u.email, u.full_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";
        
        // Execute the query
        $result = mysqli_query($this->db, $sql);
        
        // Check if query was successful
        if (!$result) {
            return [];
        }
        
        // Get all orders
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        
        return $orders;
    }

    public function getOrderById($orderId) {
        // First get the order details
        $sql = "SELECT o.*, u.username, u.email, u.full_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = '$orderId'";
        
        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return null;
        }
        
        $order = mysqli_fetch_assoc($result);
        if (!$order) {
            return null;
        }

        // Then get the order items
        $sql = "SELECT oi.*, p.name as product_name, p.image_url as image,
                oi.price_per_unit as price,
                (oi.price_per_unit * oi.quantity) as total_price
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = '$orderId'";
        
        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return $order;
        }
        
        // Get all items for this order
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        $order['items'] = $items;

        return $order;
    }

    public function updateOrderStatus($orderId, $status) {
        // Simple update query
        $sql = "UPDATE orders SET status = '$status' WHERE id = '$orderId'";
        return mysqli_query($this->db, $sql);
    }

    public function getOrdersByUserId($userId) {
        // Get orders for a specific user
        $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count 
                FROM orders o 
                WHERE o.user_id = '$userId' 
                ORDER BY o.created_at DESC";
        
        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return [];
        }
        
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        return $orders;
    }

    public function getOrderStatistics() {
        $stats = [];
        
        // Get total number of orders
        $sql = "SELECT COUNT(*) as total FROM orders";
        $result = mysqli_query($this->db, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            
            $stats['total_orders'] = $row['total'];
        }

        // Get count of orders by status
        $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $result = mysqli_query($this->db, $sql);
        if ($result) {
            $stats['orders_by_status'] = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $stats['orders_by_status'][] = $row;
            }
        }

        // Get total revenue
        $sql = "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'";
        $result = mysqli_query($this->db, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            var_dump($row);
            $stats['total_revenue'] = $row['total'];
        }

        return $stats;
    }
} 