<?php
require_once 'functions.php';

if (!isset($_GET['id'])) {
    echo "ไม่พบรหัสสินค้า";
    exit;
}

$id = intval($_GET['id']);
$product = getProductById($id);

if (!$product) {
    echo "ไม่พบข้อมูลสินค้า";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image'] ?? null;

    if (updateProduct($id, $name, $price, $image)) {
        header("Location: index.php?message=updated");
        exit;
    } else {
        $error = "เกิดข้อผิดพลาดในการอัปเดตสินค้า";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">แก้ไขสินค้า</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อสินค้า</label>
            <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">ราคาสินค้า</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required value="<?= htmlspecialchars($product['price']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">รูปภาพปัจจุบัน</label><br>
            <?php if (!empty($product['image_path']) && file_exists($product['image_path'])): ?>
                <img src="<?= $product['image_path'] ?>" class="img-thumbnail mb-2" style="max-width: 200px;">
            <?php else: ?>
                <p>ไม่มีรูปภาพ</p>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">เปลี่ยนรูปภาพ (ไม่บังคับ)</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-success">บันทึกการเปลี่ยนแปลง</button>
        <a href="index.php" class="btn btn-secondary">ย้อนกลับ</a>
    </form>
</div>
</body>
</html>
