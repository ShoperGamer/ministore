<?php
require_once 'functions.php';

// ตรวจสอบการส่งฟอร์มเพิ่มสินค้า
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    
    $image_path = null;
    
    // จัดการอัพโหลดรูปภาพ
    if (!empty($_FILES['image']['name'])) {
        $upload_result = handleImageUpload($_FILES['image']);
        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        } else {
            $upload_error = $upload_result['message'];
        }
    }
    
    if (addProduct($name, $price, $quantity, $image_path)) {
        $success_message = "เพิ่มสินค้าเรียบร้อยแล้ว!";
    } else {
        $error_message = "เกิดข้อผิดพลาดในการเพิ่มสินค้า";
    }
}

// ดึงข้อมูลสินค้า (พร้อมการค้นหา)
$search = $_GET['search'] ?? null;
$min_price = $_GET['min_price'] ?? null;
$max_price = $_GET['max_price'] ?? null;

$products = getAllProducts($search, $min_price, $max_price);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการสินค้าร้านค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient shadow-sm" style="background: linear-gradient(135deg, #2c3e50, #3498db);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-shop-window me-2"></i>ระบบจัดการสินค้า
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="bi bi-house-door me-1"></i> หน้าหลัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-box-seam me-1"></i> สินค้าทั้งหมด</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-gear me-1"></i> ตั้งค่าระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-primary mb-3">
                <i class="bi bi-cart4 me-2"></i>ระบบจัดการสินค้าร้านค้า
            </h1>
            <p class="lead text-muted">จัดการข้อมูลสินค้าในร้านค้าของคุณได้อย่างง่ายดาย</p>
        </div>
        
        <!-- Alert Messages -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
            <?php if ($_GET['message'] ?? '' === 'updated'): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm">
        <i class="bi bi-check-circle-fill me-2"></i> อัปเดตสินค้าสำเร็จแล้ว
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif ($_GET['message'] ?? '' === 'deleted'): ?>
    <div class="alert alert-info alert-dismissible fade show shadow-sm">
        <i class="bi bi-info-circle-fill me-2"></i> ลบสินค้าเรียบร้อยแล้ว
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($upload_error)): ?>
                    <div class="alert alert-warning alert-dismissible fade show shadow-sm">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i><?php echo $upload_error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- ฟอร์มเพิ่มสินค้า -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="bi bi-plus-circle me-2"></i>เพิ่มสินค้าใหม่
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">ชื่อสินค้า</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="กรุณากรอกชื่อสินค้า" required>
                                    <div class="invalid-feedback">กรุณากรอกชื่อสินค้า</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label fw-semibold">ราคา</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="0.00" required>
                                    <span class="input-group-text bg-light">บาท</span>
                                    <div class="invalid-feedback">กรุณากรอกราคาสินค้า</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-semibold">จำนวนคงเหลือ</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-box-seam"></i></span>
                                    <input type="number" class="form-control" id="quantity" name="quantity" placeholder="0" required>
                                    <span class="input-group-text bg-light">ชิ้น</span>
                                    <div class="invalid-feedback">กรุณากรอกจำนวนสินค้า</div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="image" class="form-label fw-semibold">รูปภาพสินค้า</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>
                                <div class="mt-2" id="image-preview-container" style="display:none;">
                                    <img id="image-preview" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                                <small class="text-muted">ขนาดไฟล์ไม่เกิน 2MB (JPG, PNG, GIF)</small>
                            </div>
                            <button type="submit" name="add_product" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="bi bi-save me-2"></i>บันทึกสินค้า
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- รายการสินค้าและการค้นหา -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="bi bi-list-ul me-2"></i>รายการสินค้าทั้งหมด
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- ฟอร์มค้นหา -->
                        <form method="GET" class="mb-4 bg-light p-3 rounded shadow-sm">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">ค้นหาด้วยชื่อ</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control" name="search" placeholder="ชื่อสินค้า..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">ราคาต่ำสุด</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">฿</span>
                                        <input type="number" step="0.01" class="form-control" name="min_price" placeholder="0.00" value="<?php echo htmlspecialchars($min_price ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">ราคาสูงสุด</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">฿</span>
                                        <input type="number" step="0.01" class="form-control" name="max_price" placeholder="0.00" value="<?php echo htmlspecialchars($max_price ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="index.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>ล้างการค้นหา
                                </a>
                            </div>
                        </form>
                        
                        <!-- ตารางสินค้า -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80" class="text-center">รหัส</th>
                                        <th width="120" class="text-center">รูปภาพ</th>
                                        <th>ชื่อสินค้า</th>
                                        <th width="120" class="text-end">ราคา</th>
                                        <th width="120" class="text-center">คงเหลือ</th>
                                        <th width="100" class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($products)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-exclamation-circle fs-4 d-block mb-2"></i>
                                                ไม่พบสินค้าในระบบ
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($products as $product): ?>
                                            <tr class="<?php echo $product['quantity'] == 0 ? 'table-danger' : ''; ?>">
                                                <td class="text-center fw-bold"><?php echo $product['id']; ?></td>
                                                <td class="text-center">
                                                    <?php if (!empty($product['image_path'])): ?>
                                                        <img src="<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-thumbnail rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                            <i class="bi bi-image text-muted fs-4"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"><?php echo htmlspecialchars($product['name']); ?></div>
                                                    <small class="text-muted">เพิ่มเมื่อ: <?php echo date('d/m/Y', strtotime($product['created_at'])); ?></small>
                                                </td>
                                                <td class="text-end fw-bold text-success"><?php echo number_format($product['price'], 2); ?> ฿</td>
                                                <td class="text-center">
                                                    <span class="badge <?php echo $product['quantity'] > 10 ? 'bg-success' : ($product['quantity'] > 0 ? 'bg-warning text-dark' : 'bg-danger'); ?> rounded-pill px-3 py-2">
                                                        <?php echo $product['quantity']; ?> ชิ้น
                                                    </span>
                                                </td>
                                                <td class="text-center">
    <div class="btn-group btn-group-sm" role="group">
        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary" title="แก้ไข">
            <i class="bi bi-pencil"></i>
        </a>
        <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-danger" title="ลบ"
           onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?');">
            <i class="bi bi-trash"></i>
        </a>
    </div>
</td>

                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">แสดง <?php echo count($products); ?> รายการ</small>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-printer me-1"></i>พิมพ์รายงาน
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-shop me-2"></i>ระบบจัดการสินค้า</h5>
                    <p class="text-muted">ระบบจัดการสินค้าร้านค้าออนไลน์ที่ง่ายและมีประสิทธิภาพ</p>
                </div>
                <div class="col-md-3">
                    <h5>เมนู</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none text-muted">หน้าหลัก</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">สินค้าทั้งหมด</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">รายงาน</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>ติดต่อเรา</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="bi bi-envelope me-2"></i> contact@example.com</li>
                        <li><i class="bi bi-telephone me-2"></i> 02-123-4567</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="text-center text-muted">
                <small>© <?php echo date('Y'); ?> ระบบจัดการสินค้า. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    
</body>
</html>