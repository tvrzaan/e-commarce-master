<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    public function login(array $data): array {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        $user = User::findByEmail($email);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        if (!$user->verifyPassword($data['password'])) {
            return ['success' => false, 'message' => 'Invalid password'];
        }

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();

        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()
            ]
        ];
    }

    public function register(array $data): array {
        // Validate input
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        if (strlen($data['password']) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters'];
        }

        if (empty($data['username'])) {
            return ['success' => false, 'message' => 'Username is required'];
        }

        // Check if user already exists
        if (User::findByEmail($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Create new user
        $user = new User();
        $success = $user->create([
            'username' => $data['username'],
            'email' => $email,
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null
        ]);

        if (!$success) {
            return ['success' => false, 'message' => 'Registration failed'];
        }

        return ['success' => true, 'message' => 'Registration successful'];
    }

    public function logout(): void {
        session_destroy();
        session_start();
    }
} 