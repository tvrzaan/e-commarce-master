<?php
namespace App\Config;

class Database {
    private static $instance = null;
    private $connection;

    // Database configuration
    private $host = 'localhost';
    private $dbname = 'ecommerce_db';
    private $username = 'root';
    private $password = '';

    private function __construct() {
        try {
            $this->connection = mysqli_connect(
                $this->host,
                $this->username,
                $this->password,
                $this->dbname
            );

            if (!$this->connection) {
                throw new \Exception("Connection failed: " . mysqli_connect_error());
            }

            // Set charset to ensure proper encoding
            mysqli_set_charset($this->connection, "utf8mb4");
        } catch (\Exception $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    private function __clone() {}
    
    public function __wakeup() {}
} 