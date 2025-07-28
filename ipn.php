<?php
file_put_contents(__DIR__ . "/ipn_log.txt", date("Y-m-d H:i:s") . " | RAW: " . file_get_contents("php://input") . "\n", FILE_APPEND);
require 'db.php';

// بررسی امنیتی IPN Secret
$received_hmac = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'] ?? '';
$ipn_secret = 'Sug/qfzKLqbKx/SFWrlIMLzofCQ4kAqe'; // 👈 این همون IPN Security Code تو هست

$body = file_get_contents("php://input");
$calculated_hmac = hash_hmac('sha512', $body, trim($ipn_secret));

if (!hash_equals($calculated_hmac, $received_hmac)) {
    http_response_code(403);
    die("❌ درخواست نامعتبر (هش تطابق ندارد).");
}

$data = json_decode($body, true);

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

    $message = null;
    foreach ($messages as &$item) {
        if (isset($item['used']) && $item['used'] === false) {
            $message = $item;
            $item['used'] = true;
            break;
        }
    }

    if (!$message) {
        die("❌ پیام استفاده‌نشده‌ای باقی نمانده است.");
    }

    file_put_contents($json_file, json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $stmt = $pdo->prepare("UPDATE orders SET status = 'paid', email = :email, password = :password WHERE order_id = :order_id");
    $stmt->execute([
        'order_id' => $order_id,
        'email' => $message['email'],
        'password' => $message['password']
    ]);

    echo "✅ پرداخت تأیید شد<br>";
    echo "<strong>ایمیل:</strong> " . htmlspecialchars($message['email']) . "<br>";
    echo "<strong>رمز:</strong> " . htmlspecialchars($message['password']) . "<br>";

} catch (PDOException $e) {
    http_response_code(500);
    die("❌ خطا در پردازش سفارش: " . $e->getMessage());
}
?>
