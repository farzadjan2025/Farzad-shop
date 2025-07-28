<?php
require 'db.php';

$order_id = $_GET['order_id'] ?? null;

// بررسی صحت شناسه سفارش
if (!$order_id || !preg_match('/^order_[a-zA-Z0-9]+$/', $order_id)) {
    echo "❌ شناسه سفارش نامعتبر است.";
    exit;
}

// گرفتن سفارش از دیتابیس
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order || $order['status'] !== 'paid') {
    echo "❌ پرداخت موفق نبود یا سفارش یافت نشد.";
    exit;
}

// بررسی وجود ایمیل و رمز
if (empty($order['email']) || empty($order['password'])) {
    echo "❌ اطلاعات محصول برای این سفارش ثبت نشده است.";
    exit;
}

// نمایش فقط ایمیل و رمز سفارش مربوطه
echo "<h2>🎉 خرید موفق بود!</h2>";
echo "<p><strong>ایمیل:</strong> " . htmlspecialchars($order['email']) . "<br>";
echo "<strong>رمز:</strong> " . htmlspecialchars($order['password']) . "</p>";
?>
