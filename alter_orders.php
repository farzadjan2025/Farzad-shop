<?php
require 'db.php';

try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN email VARCHAR(255), ADD COLUMN password VARCHAR(255)");
    echo "✅ ستون‌های email و password با موفقیت به جدول orders اضافه شدند.";
} catch (PDOException $e) {
    echo "❌ خطا در اجرای تغییرات: " . $e->getMessage();
}
?>
