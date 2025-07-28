<?php
// success.php
$order_id = $_GET['order_id'] ?? '';

if (!$order_id) {
    echo "Invalid order ID";
    exit;
}

$db = new SQLite3(__DIR__ . '/orders.db');
$stmt = $db->prepare("SELECT * FROM orders WHERE order_id = :order_id");
$stmt->bindValue(':order_id', $order_id, SQLITE3_TEXT);
$result = $stmt->execute();
$order = $result->fetchArray(SQLITE3_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit;
}

// وضعیت‌هایی که به عنوان موفقیت آمیز در نظر گرفته می‌شوند
$success_statuses = ['paid', 'partially_paid', 'finished', 'confirming'];

if (in_array($order['payment_status'], $success_statuses)) {
    echo "<h1>پرداخت شما دریافت شد.</h1>";
    echo "<p>آی‌دی سفارش شما: <strong>{$order['order_id']}</strong></p>";
    echo "<p>وضعیت پرداخت: <strong>{$order['payment_status']}</strong></p>";
    echo "<p>لینک محصول:</p>";
    echo "<a href='{$order['product_url']}'>دانلود محصول</a>";
} else {
    echo "<h1>در حال انتظار برای تایید پرداخت...</h1>";
    echo "<p>وضعیت کنونی: <strong>{$order['payment_status']}</strong></p>";
    echo "<p>لطفاً چند لحظه دیگر این صفحه را بازبینی کنید.</p>";
}
?>
