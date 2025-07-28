<?php
require 'db.php';

$order_id = $_GET['order_id'] ?? '';
if (!$order_id) {
    exit("شناسه سفارش معتبر نیست.");
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    exit("سفارش پیدا نشد.");
}

// بررسی وضعیت معتبر
$valid_statuses = ['paid', 'confirmed', 'confirming', 'partially_paid', 'finished'];
if (!in_array(strtolower($order['status']), $valid_statuses)) {
    exit("پرداخت هنوز کامل نشده است. وضعیت فعلی: " . htmlspecialchars($order['status']));
}

if (empty($order['email']) || empty($order['password'])) {
    exit("پرداخت موفق بود ولی هنوز محصول آماده نشده است. لطفاً کمی بعد دوباره تلاش کنید.");
}

// نمایش پیام محصول
echo "<h2>پرداخت موفق!</h2>";
echo "<p><strong>ایمیل:</strong> {$order['email']}</p>";
echo "<p><strong>رمز عبور:</strong> {$order['password']}</p>";
?>
