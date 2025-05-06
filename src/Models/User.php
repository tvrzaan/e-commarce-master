<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    protected ?int $id = null;
    protected string $username;
    protected string $email;
    protected string $password;
    protected ?string $phone = null;
    protected ?string $address = null;
    protected string $role = 'user';
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(array $data): bool {
        $sql = "INSERT INTO users (username, email, password, phone, address, role) 
                VALUES (:username, :email, :password, :phone, :address, :role)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role' => $data['role'] ?? 'user'
        ]);
    }

    public static function findByEmail(string $email): ?self {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $instance = new self();
            foreach ($user as $key => $value) {
                if (property_exists($instance, $key)) {
                    $instance->$key = $value;
                }
            }
            return $instance;
        }
        
        return null;
    }

    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    public function update(array $data): bool {
        $updates = [];
        $params = [];

        foreach ($data as $key => $value) {
            if (property_exists($this, $key) && $key !== 'id') {
                $updates[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
        $params['id'] = $this->id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }
    public function getPhone(): ?string { return $this->phone; }
    public function getAddress(): ?string { return $this->address; }
} 