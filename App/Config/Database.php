<?php
namespace App\Config;

use PDO;
use PDOException;

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
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }

    private function __clone() {}
    
    public function __wakeup() {}
} 