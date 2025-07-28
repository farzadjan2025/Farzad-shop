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

// قبول وضعیت "paid" یا "confirming" یا "confirmed" یا "partially_paid"
$allowed_statuses = ['paid', 'confirming', 'confirmed', 'partially_paid'];

if (!in_array(strtolower($order['status']), $allowed_statuses)) {
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
