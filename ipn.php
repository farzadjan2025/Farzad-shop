<?php
require 'db.php';

$rawData = file_get_contents('php://input');
file_put_contents(__DIR__ . '/ipn_log.txt', date('Y-m-d H:i:s') . " | RAW: $rawData\n", FILE_APPEND);

// فایل جداگانه برای دیباگ مرحله به مرحله
function log_debug($msg) {
    file_put_contents(__DIR__ . '/ipn_debug.txt', date('Y-m-d H:i:s') . " | $msg\n", FILE_APPEND);
}

log_debug("🔔 IPN دریافت شد.");

$data = json_decode($rawData, true);
if (!$data || !isset($data['order_id'])) {
    log_debug("❌ داده نامعتبر یا بدون order_id.");
    http_response_code(400);
    exit("Invalid data");
}

$order_id = $data['order_id'];
$status = strtolower($data['payment_status'] ?? 'unknown');

log_debug("📦 order_id دریافت شد: $order_id - وضعیت: $status");

// فقط این وضعیت‌ها پذیرفته می‌شوند
$acceptable_statuses = ['paid', 'confirmed', 'confirming', 'partially_paid', 'finished'];
if (!in_array($status, $acceptable_statuses)) {
    log_debug("⚠️ وضعیت $status قابل قبول نیست.");
    http_response_code(200);
    exit("Status not acceptable");
}

// پیدا کردن سفارش
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    log_debug("❌ سفارش پیدا نشد.");
    http_response_code(404);
    exit("Order not found");
}

log_debug("✅ سفارش پیدا شد: ID={$order['id']}");

// بررسی اینکه قبلاً اطلاعات ایمیل/رمز ذخیره نشده
if (!empty($order['email']) && !empty($order['password'])) {
    log_debug("ℹ️ سفارش قبلاً پردازش شده. (ایمیل و رمز وجود دارد)");
    http_response_code(200);
    exit("Already processed");
}

// بررسی فایل پیام
$product_id = $order['product_id'];
$json_file = __DIR__ . "/messages/$product_id.json";
if (!file_exists($json_file)) {
    log_debug("❌ فایل پیام $product_id.json پیدا نشد.");
    http_response_code(500);
    exit("Product file not found");
}

log_debug("✅ فایل پیام $product_id.json پیدا شد.");

// پیام‌ها را بخوان
$messages = json_decode(file_get_contents($json_file), true);
$found = false;
foreach ($messages as &$msg) {
    if (!$msg['used'] && floatval($msg['price']) <= floatval($order['price'])) {
        $found = true;
        $email = $msg['email'];
        $password = $msg['password'];
        $msg['used'] = true;
        break;
    }
}

if (!$found) {
    log_debug("❌ هیچ پیام آزاد با قیمت مناسب پیدا نشد.");
    http_response_code(500);
    exit("No available message");
}

// ذخیره در جدول سفارش
$update = $pdo->prepare("UPDATE orders SET status = ?, email = ?, password = ? WHERE order_id = ?");
$success = $update->execute([$status, $email, $password, $order_id]);

if ($success) {
    log_debug("✅ سفارش بروزرسانی شد: status=$status, email=$email");
} else {
    $errorInfo = $update->errorInfo();
    log_debug("❌ خطا در به‌روزرسانی سفارش: " . print_r($errorInfo, true));
    http_response_code(500);
    exit("Database update failed");
}

// ذخیره پیام‌ها با تغییر used
file_put_contents($json_file, json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
log_debug("✅ پیام علامت‌گذاری شد به عنوان used.");

http_response_code(200);
echo "✅ Success";
?>
