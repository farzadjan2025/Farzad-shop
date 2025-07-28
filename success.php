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

if (!$order || !in_array($order['status'], ['paid', 'partially_paid'])) {
    echo "⏳ پرداخت هنوز نهایی نشده است. لطفاً چند لحظه دیگر دوباره امتحان کنید.";
    exit;
}

if (!$order['email'] || !$order['password']) {
    echo "❌ اطلاعات محصول ثبت نشده است.";
    exit;
}

echo "<h2>🎉 خرید موفق بود!</h2>";
echo "<p><strong>ایمیل:</strong> " . htmlspecialchars($order['email']) . "<br>";
echo "<strong>رمز:</strong> " . htmlspecialchars($order['password']) . "</p>";
?>
