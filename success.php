<?php
require 'db.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "❌ شناسه سفارش یافت نشد.";
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

// خواندن اطلاعات محصول از فایل JSON
$product_id = $order['product_id'];
$json_file = "messages/" . basename($product_id) . ".json";

if (file_exists($json_file)) {
    $json_data = json_decode(file_get_contents($json_file), true);
    if (is_array($json_data) && count($json_data) > 0) {
        echo "<h2>🎉 خرید موفق بود!</h2>";
        foreach ($json_data as $index => $item) {
            echo "<p><strong>ایمیل:</strong> {$item['email']}<br>";
            echo "<strong>رمز:</strong> {$item['password']}</p><hr>";
        }
    } else {
        echo "❌ فایل محصول خالی است.";
    }
} else {
    echo "❌ فایل محصول یافت نشد.";
}
?>