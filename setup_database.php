<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Create connection
    $conn = new mysqli($host, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS ecommerce_db";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully<br>";
    } else {
        echo "Error creating database: " . $conn->error . "<br>";
    }
    
    // Select database
    $conn->select_db("ecommerce_db");
    
    // Create users table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        phone VARCHAR(20),
        address TEXT,
        role VARCHAR(20) DEFAULT 'user',
        gender ENUM('male', 'female') DEFAULT NULL,
        birthday DATE DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "Users table created successfully<br>";
    } else {
        echo "Error creating users table: " . $conn->error . "<br>";
    }

    // Create categories table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        parent_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql) === TRUE) {
        echo "Categories table created successfully<br>";
    } else {
        echo "Error creating categories table: " . $conn->error . "<br>";
    }

    // Create products table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock_quantity INT NOT NULL DEFAULT 0,
        category_id INT,
        image_url VARCHAR(255),
        sku VARCHAR(50) UNIQUE,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql) === TRUE) {
        echo "Products table created successfully<br>";
    } else {
        echo "Error creating products table: " . $conn->error . "<br>";
    }

    // Create orders table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        shipping_address TEXT NOT NULL,
        payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql) === TRUE) {
        echo "Orders table created successfully<br>";
    } else {
        echo "Error creating orders table: " . $conn->error . "<br>";
    }

    // Create order_items table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price_per_unit DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql) === TRUE) {
        echo "Order items table created successfully<br>";
    } else {
        echo "Error creating order items table: " . $conn->error . "<br>";
    }

    // Create cart table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql) === TRUE) {
        echo "Cart table created successfully<br>";
    } else {
        echo "Error creating cart table: " . $conn->error . "<br>";
    }

    // Create product_reviews table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS product_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        user_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql) === TRUE) {
        echo "Product reviews table created successfully<br>";
    } else {
        echo "Error creating product reviews table: " . $conn->error . "<br>";
    }

    // Check if admin user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $adminEmail = 'youssef@gmail.com';
    $stmt->bind_param("s", $adminEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Create admin user
        $adminPassword = password_hash('#8.}MhpivP?+2-}', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, full_name, role) 
                VALUES ('youssef', ?, ?, 'Youssef Admin', 'admin')";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ss", $adminEmail, $adminPassword);
            
            if ($stmt->execute()) {
                echo "<div style='color: green;'>Admin user created successfully!<br>";
                echo "Login credentials:<br>";
                echo "Email: youssef@gmail.com<br>";
                echo "Password: #8.}MhpivP?+2-}</div><br>";
            } else {
                echo "Error creating admin user: " . $stmt->error . "<br>";
            }
        }
    } else {
        echo "Admin user already exists<br>";
    }
    
    $conn->close();
    
    echo "<div class='alert alert-success'>Database setup completed successfully!</div>";
    echo "<br><a href='views/auth/login.php' class='btn btn-primary'>Go to Login Page</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?> 