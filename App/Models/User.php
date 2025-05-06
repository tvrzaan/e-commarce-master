<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createUser($data) {
        // Check if email already exists
        if ($this->isEmailTaken($data['email'])) {
            return false;
        }

        $sql = "INSERT INTO users (username, email, password, full_name, phone, address, created_at) 
                VALUES (:username, :email, :password, :full_name, :phone, :address, NOW())";

        $params = [
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':full_name' => $data['full_name'],
            ':phone' => $data['phone'] ?? '',
            ':address' => $data['address'] ?? ''
        ];

        try {
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);
            return $success ? $this->db->lastInsertId() : false;
        } catch (\PDOException $e) {
            // Log error if needed
            return false;
        }
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id, username, email, full_name, phone, address, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data) {
        $sql = "UPDATE users SET 
                full_name = :full_name,
                email = :email,
                phone = :phone,
                address = :address";

        // If password is being updated, add it to the query
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
        }

        $sql .= " WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':address' => $data['address']
        ];

        // Add hashed password to params if it's being updated
        if (!empty($data['password'])) {
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updatePassword($id, $currentPassword, $newPassword) {
        // First verify current password
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return false;
        }

        // Update to new password
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }

    public function isEmailTaken($email, $excludeUserId = null) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $params = [':email' => $email];

        if ($excludeUserId) {
            $sql .= " AND id != :user_id";
            $params[':user_id'] = $excludeUserId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
} 