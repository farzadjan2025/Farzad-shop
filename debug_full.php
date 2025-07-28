<?php
require 'db.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "❌ شناسه سفارش داده نشده است.";
    exit;
}

echo "<h3>🔍 بررسی کامل سفارش: <code>$order_id</code></h3>";

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "❌ سفارش یافت نشد در دیتابیس.<br>";
    exit;
}

echo "<strong>✅ سفارش پیدا شد:</strong><br><pre>";
print_r($order);
echo "</pre>";

$status = strtolower($order['status']);
$allowed_statuses = ['paid', 'confirming', 'confirmed', 'partially_paid'];

if (!in_array($status, $allowed_statuses)) {
    echo "⚠️ وضعیت سفارش هنوز قابل قبول نیست: <strong>$status</strong><br>";
} else {
    echo "✅ وضعیت سفارش قابل قبول است: <strong>$status</strong><br>";
}

$product_id = $order['product_id'];
$json_file = __DIR__ . "/messages/{$product_id}.json";

if (!file_exists($json_file)) {
    echo "❌ فایل پیام <code>$product_id.json</code> وجود ندارد.<br>";
    exit;
}

echo "✅ فایل پیام پیدا شد: <code>$product_id.json</code><br>";

$messages = json_decode(file_get_contents($json_file), true);

if (!is_array($messages) || empty($messages)) {
    echo "❌ محتوای فایل پیام نامعتبر یا خالی است.<br>";
    exit;
}

$actually_paid = floatval($order['price']);
$matched = false;

foreach ($messages as $index => $msg) {
    $price = floatval($msg['price'] ?? 0);
    $used = $msg['used'] ?? null;
    $email = $msg['email'] ?? '-';

    echo "🔸 پیام #" . ($index + 1) . ": قیمت = $price | used = " . var_export($used, true) . " | ایمیل = $email<br>";

    if ($used === false && abs($price - $actually_paid) <= 0.01) {
        $matched = true;
        echo "<strong>✅ این پیام می‌توانست انتخاب شود.</strong><br>";
    }
}

if (!$matched) {
    echo "❌ هیچ پیام مناسب (از نظر قیمت و used=false) پیدا نشد.<br>";
}

if (!$order['email'] || !$order['password']) {
    echo "⚠️ هنوز اطلاعات پیام (ایمیل یا رمز) در جدول سفارش ذخیره نشده است.<br>";
} else {
    echo "✅ اطلاعات پیام در سفارش ذخیره شده:<br>";
    echo "<strong>ایمیل:</strong> " . htmlspecialchars($order['email']) . "<br>";
    echo "<strong>رمز:</strong> " . htmlspecialchars($order['password']) . "<br>";
}

echo "<br>🔚 بررسی کامل انجام شد.";
?>
