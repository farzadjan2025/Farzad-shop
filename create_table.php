<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    order_id VARCHAR(255) NOT NULL,
    product_id VARCHAR(255) NOT NULL,
    price NUMERIC(10, 2) NOT NULL,
    status VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $pdo->exec($sql);
    echo "✅ جدول orders با موفقیت ساخته شد.";
} catch (PDOException $e) {
    echo "❌ خطا در ساخت جدول: " . $e->getMessage();
}
?>
