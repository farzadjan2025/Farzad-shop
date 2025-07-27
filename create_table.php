<?php
require 'db.php';

try {
    $sql = "
        CREATE TABLE IF NOT EXISTS orders (
            id SERIAL PRIMARY KEY,
            order_id VARCHAR(50) UNIQUE NOT NULL,
            product_id VARCHAR(100) NOT NULL,
            price NUMERIC(10, 2) NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ";
    $pdo->exec($sql);
    echo "✅ جدول orders ساخته شد.";
} catch (PDOException $e) {
    echo "❌ خطا در ساخت جدول: " . $e->getMessage();
}
?>