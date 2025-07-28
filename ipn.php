<?php
file_put_contents(__DIR__ . "/ipn_log.txt", date("Y-m-d H:i:s") . " | RAW: " . file_get_contents("php://input") . "\n", FILE_APPEND);
require 'db.php';

// ادامه کد بدون تغییر...

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['payment_status']) || !isset($data['order_id'])) {
    http_response_code(400);
    die("❌ داده نامعتبر یا ناقص.");
}

$payment_status = $data['payment_status'];
$order_id = $data['order_id'];

// فقط اگر وضعیت پرداخت "processing" باشد ادامه بده
if ($payment_status !== 'processing') {
    http_response_code(200);
    die("⏳ هنوز در انتظار پرداخت هستیم...");
}

try {
    // پیدا کردن سفارش
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id LIMIT 1");
    $stmt->execute(['order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);
        die("❌ سفارش یافت نشد.");
    }

    // اگر قبلاً پردازش شده بود، دوباره پیام نده
    if ($order['status'] === 'paid') {
        die("✅ این سفارش قبلاً پردازش شده است.");
    }

    $product_id = $order['product_id'];
    $json_file = __DIR__ . "/messages/{$product_id}.json";

    if (!file_exists($json_file)) {
        die("❌ فایل پیام محصول یافت نشد.");
    }

    $messages = json_decode(file_get_contents($json_file), true);

    if (!is_array($messages) || empty($messages)) {
        die("❌ پیام موجود نیست.");
    }

    // پیدا کردن اولین پیام با used = false
    $message = null;
    foreach ($messages as &$item) {
        if (isset($item['used']) && $item['used'] === false) {
            $message = $item;
            $item['used'] = true; // علامت‌گذاری به عنوان استفاده‌شده
            break;
        }
    }

    if (!$message) {
        die("❌ پیام استفاده‌نشده‌ای باقی نمانده است.");
    }

    // ذخیره پیام استفاده‌شده در فایل
    file_put_contents($json_file, json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // به‌روزرسانی وضعیت سفارش + ذخیره ایمیل و رمز تحویلی
    $stmt = $pdo->prepare("UPDATE orders SET status = 'paid', email = :email, password = :password WHERE order_id = :order_id");
    $stmt->execute([
        'order_id' => $order_id,
        'email' => $message['email'],
        'password' => $message['password']
    ]);

    // نمایش پیام محصول
    echo "✅ پرداخت تأیید شد<br>";
    echo "<strong>ایمیل:</strong> " . htmlspecialchars($message['email']) . "<br>";
    echo "<strong>رمز:</strong> " . htmlspecialchars($message['password']) . "<br>";

} catch (PDOException $e) {
    http_response_code(500);
    die("❌ خطا در پردازش سفارش: " . $e->getMessage());
}
?>
