<?php
require_once 'functions.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if (deleteProduct($id)) {
        header("Location: index.php?message=deleted");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการลบสินค้า";
    }
} else {
    echo "ไม่พบรหัสสินค้า";
}
