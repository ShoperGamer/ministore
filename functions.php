<?php
require_once 'config.php';

function getAllProducts($search = null, $min_price = null, $max_price = null) {
    global $conn;
    
    $sql = "SELECT * FROM products";
    $conditions = [];
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $conditions[] = "name LIKE ?";
        $params[] = "%$search%";
        $types .= 's';
    }
    
    if (!empty($min_price)) {
        $conditions[] = "price >= ?";
        $params[] = $min_price;
        $types .= 'd';
    }
    
    if (!empty($max_price)) {
        $conditions[] = "price <= ?";
        $params[] = $max_price;
        $types .= 'd';
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addProduct($name, $price, $quantity, $image_path = null) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $name, $price, $quantity, $image_path);
    
    return $stmt->execute();
}

function handleImageUpload($file) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // ตรวจสอบว่าไฟล์เป็นภาพจริง
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return ['success' => false, 'message' => 'File is not an image.'];
    }
    
    // ตรวจสอบขนาดไฟล์ (ไม่เกิน 2MB)
    if ($file['size'] > 2000000) {
        return ['success' => false, 'message' => 'Sorry, your file is too large.'];
    }
    
    // อนุญาตเฉพาะบางนามสกุลไฟล์
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        return ['success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.'];
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'path' => $target_file];
    } else {
        return ['success' => false, 'message' => 'Sorry, there was an error uploading your file.'];
    }
}

//แก้ไขข้อมูล
function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateProduct($id, $name, $price, $image = null) {
    global $pdo;

    // อัปโหลดรูปใหม่ถ้ามี
    $imagePath = null;
    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $imagePath = 'uploads/' . uniqid() . '.' . $ext;
        move_uploaded_file($image['tmp_name'], $imagePath);

        // ลบรูปเก่า
        $old = getProductById($id);
        if ($old && !empty($old['image_path']) && file_exists($old['image_path'])) {
            unlink($old['image_path']);
        }
    }

    if ($imagePath) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, image_path = ? WHERE id = ?");
        return $stmt->execute([$name, $price, $imagePath, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
        return $stmt->execute([$name, $price, $id]);
    }
}

//ลบข้อมูล
function deleteProduct($id) {
    global $conn; // เปลี่ยนจาก $pdo เป็น $conn ที่ใช้ MySQLi
    
    // ดึง path ของรูปภาพก่อนลบ (ถ้ามี)
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // ลบรูปภาพจากโฟลเดอร์
    if ($product && !empty($product['image_path']) && file_exists($product['image_path'])) {
        unlink($product['image_path']);
    }

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}


?>

