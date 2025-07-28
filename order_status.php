<?php
// order_status.php

if (!isset($_GET['order_id'])) {
    echo "شناسه سفارش ارسال نشده است.";
    exit;
}

$order_id = $_GET['order_id'];
$databaseFile = 'orders.db';

if (!file_exists($databaseFile)) {
    echo "فایل دیتابیس وجود ندارد.";
    exit;
}

try {
    $pdo = new PDO("sqlite:$databaseFile");
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        echo "<h3>وضعیت سفارش:</h3>";
        echo "وضعیت پرداخت: " . htmlspecialchars($order['status']) . "<br>";
        echo "ایمیل: " . htmlspecialchars($order['email']) . "<br>";
        echo "پسورد: " . htmlspecialchars($order['password']) . "<br>";
    } else {
        echo "سفارش با این شناسه پیدا نشد.";
    }

} catch (PDOException $e) {
    echo "خطا در اتصال به دیتابیس: " . $e->getMessage();
}
?>
