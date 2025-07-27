<?php
require 'db.php';

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();

echo "<h1>لیست سفارش‌ها</h1>";
foreach ($orders as $order) {
    echo "<div style='margin-bottom:20px;'>";
    echo "Order ID: " . htmlspecialchars($order['order_id']) . "<br>";
    echo "Product ID: " . htmlspecialchars($order['product_id']) . "<br>";
    echo "Price: $" . htmlspecialchars($order['price']) . "<br>";
    echo "Status: " . htmlspecialchars($order['status']) . "<br>";
    echo "Created At: " . htmlspecialchars($order['created_at']) . "<br>";
    echo "</div>";
}
?>