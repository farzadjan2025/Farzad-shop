<?php
require 'db.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "❌ شناسه سفارش یافت نشد.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "❌ سفارش یافت نشد.";
    exit;
}

echo "<h3>🔍 اطلاعات سفارش:</h3>";
echo "<p><strong>Order ID:</strong> " . htmlspecialchars($order['order_id']) . "<br>";
echo "<strong>Status:</strong> " . htmlspecialchars($order['status']) . "<br>";
echo "<strong>Email:</strong> " . htmlspecialchars($order['email']) . "<br>";
echo "<strong>Password:</strong> " . htmlspecialchars($order['password']) . "</p>";
?>
