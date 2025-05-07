<?php
namespace App\Models;

use App\Config\Database;

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

        // Escape special characters
        $username = mysqli_real_escape_string($this->db, $data['username']);
        $email = mysqli_real_escape_string($this->db, $data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $fullName = mysqli_real_escape_string($this->db, $data['full_name']);
        $phone = mysqli_real_escape_string($this->db, $data['phone'] ?? '');
        $address = mysqli_real_escape_string($this->db, $data['address'] ?? '');

        // Simple insert query
        $sql = "INSERT INTO users (username, email, password, full_name, phone, address, created_at) 
                VALUES ('$username', '$email', '$password', '$fullName', '$phone', '$address', NOW())";

        try {
            $result = mysqli_query($this->db, $sql);
            return $result ? mysqli_insert_id($this->db) : false;
        } catch (\Exception $e) {
            // Log error if needed
            return false;
        }
    }

    public function getUserById($id) {
        // Simple query to get user details
        $sql = "SELECT id, username, email, full_name, phone, address, created_at 
                FROM users WHERE id = '$id'";
        
        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return null;
        }
        
        return mysqli_fetch_assoc($result);
    }

    public function updateUser($id, $data) {
        // Escape special characters
        $fullName = mysqli_real_escape_string($this->db, $data['full_name']);
        $email = mysqli_real_escape_string($this->db, $data['email']);
        $phone = mysqli_real_escape_string($this->db, $data['phone']);
        $address = mysqli_real_escape_string($this->db, $data['address']);

        // Start building the update query
        $sql = "UPDATE users SET 
                full_name = '$fullName',
                email = '$email',
                phone = '$phone',
                address = '$address'";

        // Add password update if provided
        if (!empty($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $sql .= ", password = '$password'";
        }

        // Complete the query
        $sql .= " WHERE id = '$id'";
        
        return mysqli_query($this->db, $sql);
    }

    public function updatePassword($id, $currentPassword, $newPassword) {
        // First verify current password
        $sql = "SELECT password FROM users WHERE id = '$id'";
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return false;
        }
        
        $user = mysqli_fetch_assoc($result);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return false;
        }

        // Update to new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '$hashedPassword' WHERE id = '$id'";
        return mysqli_query($this->db, $sql);
    }

    public function isEmailTaken($email, $excludeUserId = null) {
        // Escape email
        $email = mysqli_real_escape_string($this->db, $email);
        
        // Build the query
        $sql = "SELECT id FROM users WHERE email = '$email'";
        
        if ($excludeUserId) {
            $sql .= " AND id != '$excludeUserId'";
        }

        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return false;
        }
        
        return mysqli_fetch_assoc($result) ? true : false;
    }
} 