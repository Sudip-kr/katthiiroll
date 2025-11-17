<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'khati_roll_db');
define('BASE_URL', '/khati_roll');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    $conn->select_db(DB_NAME);
} else {
    die("Error creating database: " . $conn->error);
}

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(15),
        role ENUM('user', 'staff', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        image_url TEXT,
        is_available BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        total_amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'active', 'completed', 'cancelled') DEFAULT 'pending',
        time_slot VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        menu_item_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        username VARCHAR(50),
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )"
];

foreach ($tables as $table_sql) {
    if (!$conn->query($table_sql)) {
        die("Error creating table: " . $conn->error);
    }
}

// Insert default admin
$admin_check = $conn->query("SELECT id FROM users WHERE role='admin' LIMIT 1");
if ($admin_check->num_rows == 0) {
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, password, email, role) VALUES ('admin', '$admin_password', 'admin@khatiroll.com', 'admin')");
}

// Insert default staff
$staff_check = $conn->query("SELECT id FROM users WHERE role='staff' LIMIT 1");
if ($staff_check->num_rows == 0) {
    $staff_password = password_hash('staff123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, password, email, role) VALUES ('staff', '$staff_password', 'staff@khatiroll.com', 'staff')");
}

// Insert sample menu items
$menu_check = $conn->query("SELECT id FROM menu_items LIMIT 1");
if ($menu_check->num_rows == 0) {
    $menu_items = [
        ['Paneer Tikka Roll', 'Delicious paneer tikka wrapped in a soft roll', 150, 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=400'],
        ['Spicy Potato Roll', 'Spicy aloo filling in a crispy roll', 120, 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=400'],
        ['Mushroom Masala Roll', 'Mushroom cooked in rich masala', 140, 'https://images.unsplash.com/photo-1567337710282-00832b415979?w=400'],
        ['Mixed Veggie Roll', 'Assorted vegetables in perfect harmony', 130, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=400'],
        ['Soya Chaap Roll', 'Protein-rich soya chaap roll', 160, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400'],
        ['Corn & Cheese Roll', 'Sweet corn with melted cheese', 145, 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400'],
        ['Chilli Paneer Roll', 'Spicy chilli paneer roll', 155, 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400'],
        ['Aloo Gobi Roll', 'Classic potato and cauliflower roll', 125, 'https://images.unsplash.com/photo-1574484284002-952d92456975?w=400']
    ];
    
    foreach ($menu_items as $item) {
        $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $item[0], $item[1], $item[2], $item[3]);
        $stmt->execute();
    }
}

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin($role = null) {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
    
    if ($role && $_SESSION['role'] != $role) {
        header('Location: ' . BASE_URL . '/');
        exit;
    }
}

function getCurrentUser() {
    global $conn;
    if (!isLoggedIn()) return null;
    
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
    return $result->fetch_assoc();
}
?>