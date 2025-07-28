<?php
require 'db.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("❌ شناسه سفارش ارسال نشده.");
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("❌ سفارش پیدا نشد.");
}

echo "<h3>وضعیت سفارش:</h3>";
echo "<pre>" . print_r($order, true) . "</pre>";
