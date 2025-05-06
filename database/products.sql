-- Drop table if exists to ensure clean structure
DROP TABLE IF EXISTS products;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(255) NOT NULL,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some sample data
INSERT INTO products (name, description, price, category, image, stock) VALUES
('Laptop Pro', 'High-performance laptop for professionals', 999.99, 'laptops', 'uploads/products/laptop-pro.jpg', 10),
('Smartphone X', 'Latest smartphone with advanced features', 699.99, 'smartphones', 'uploads/products/smartphone-x.jpg', 15),
('Tablet Ultra', 'Powerful tablet for work and entertainment', 499.99, 'tablets', 'uploads/products/tablet-ultra.jpg', 20),
('Smart Watch', 'Smart watch with health monitoring', 299.99, 'accessories', 'uploads/products/smart-watch.jpg', 25); 