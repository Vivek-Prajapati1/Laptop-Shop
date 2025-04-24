-- Create database
CREATE DATABASE IF NOT EXISTS laptopshop;
USE laptopshop;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Laptops table
CREATE TABLE IF NOT EXISTS laptops (
    laptop_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    processor VARCHAR(100) NOT NULL,
    ram VARCHAR(50) NOT NULL,
    storage VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    laptop_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (laptop_id) REFERENCES laptops(laptop_id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Order details table
CREATE TABLE IF NOT EXISTS order_details (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    laptop_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (laptop_id) REFERENCES laptops(laptop_id) ON DELETE CASCADE
);

-- Insert sample admin user
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@laptopshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample laptops
INSERT INTO laptops (name, brand, processor, ram, storage, price, image_url, stock, featured, description) VALUES
('MacBook Pro 14"', 'Apple', 'M2 Pro', '16GB', '512GB SSD', 1999.99, 'assets/macbook-pro.jpg', 10, 1, 'Powerful laptop for professionals'),
('Dell XPS 13', 'Dell', 'Intel i7', '16GB', '512GB SSD', 1299.99, 'assets/dell-xps.jpg', 15, 1, 'Premium ultrabook with stunning display'),
('Lenovo ThinkPad X1', 'Lenovo', 'Intel i7', '16GB', '1TB SSD', 1499.99, 'assets/thinkpad.jpg', 8, 1, 'Business laptop with military-grade durability'),
('HP Spectre x360', 'HP', 'Intel i7', '16GB', '512GB SSD', 1399.99, 'assets/hp-spectre.jpg', 12, 1, 'Convertible laptop with premium design'); 