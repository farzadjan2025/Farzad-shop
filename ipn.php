<?php
file_put_contents(__DIR__ . "/ipn_log.txt", date("Y-m-d H:i:s") . " | RAW: " . file_get_contents("php://input") . "\n", FILE_APPEND);
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

// بررسی امنیتی IPN
$expected_security_code = 'Sug/qfzKLqbKx/SFWrlIMLzofCQ4kAqe';
$received_code = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'] ?? '';

if ($received_code !== $expected_security_code) {
    http_response_code(403);
    die("❌ دسترسی غیرمجاز.");
}

// بررسی داده‌ها
if (!$data || !isset($data['payment_status']) || !isset($data['order_id'])) {
    http_response_code(400);
    die("❌ داده نامعتبر.");
}

$payment_status = strtolower($data['payment_status']);
$order_id = $data['order_id'];

// وضعیت‌های قابل قبول
$acceptable_statuses = ['confirming', 'partially_paid', 'paid', 'confirmed'];

if (!in_array($payment_status, $acceptable_statuses)) {
    http_response_code(200);
    die("⏳ وضعیت پرداخت هنوز قابل پردازش نیست.");
}

try {
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
        die("❌ فایل محصول یافت نشد.");
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
        die("❌ محصولی باقی نمانده است.");
    }

    file_put_contents($json_file, json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // 🚨 به‌روزرسانی سفارش در دیتابیس
    $stmt = $pdo->prepare("UPDATE orders SET status = 'paid', email = :email, password = :password WHERE order_id = :order_id");
    $stmt->execute([
        'order_id' => $order_id,
        'email' => $message['email'],
        'password' => $message['password']
    ]);

    // لاگ موفقیت
    file_put_contents(__DIR__ . "/debug.txt", date("Y-m-d H:i:s") . " | ✅ سفارش $order_id به‌روزرسانی شد.\n", FILE_APPEND);

    echo "✅ پرداخت تأیید شد<br>";
    echo "<strong>ایمیل:</strong> " . htmlspecialchars($message['email']) . "<br>";
    echo "<strong>رمز:</strong> " . htmlspecialchars($message['password']) . "<br>";

} catch (PDOException $e) {
    http_response_code(500);
    file_put_contents(__DIR__ . "/debug.txt", date("Y-m-d H:i:s") . " | ❌ خطا: " . $e->getMessage() . "\n", FILE_APPEND);
    die("❌ خطا در پردازش: " . $e->getMessage());
}
?>
