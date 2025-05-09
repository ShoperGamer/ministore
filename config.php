<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ministore';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// สร้างตารางถ้ายังไม่มี
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

// เพิ่มข้อมูลตัวอย่างถ้าตารางว่าง
$check = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $check->fetch_assoc();
if ($row['count'] == 0) {
    $sample_data = [
        ['name' => 'น้ำดื่ม', 'price' => 10.00, 'quantity' => 50],
        ['name' => 'ขนมปัง', 'price' => 25.00, 'quantity' => 20],
        ['name' => 'นมสด', 'price' => 18.00, 'quantity' => 30]
    ];
    
    foreach ($sample_data as $item) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $item['name'], $item['price'], $item['quantity']);
        $stmt->execute();
    }
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // สร้างตารางถ้ายังไม่มี
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>