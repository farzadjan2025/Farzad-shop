<?php
require 'db.php';

$product_id = $_GET['product_id'] ?? '';
if (!$product_id) {
    exit('شناسه محصول ارسال نشده است.');
}

$product_file = __DIR__ . "/messages/{$product_id}.json";
if (!file_exists($product_file)) {
    exit('شناسه محصول نامعتبر است.');
}

$messages = json_decode(file_get_contents($product_file), true);
if (!$messages || !is_array($messages)) {
    exit('خطا در خواندن فایل پیام.');
}

// پیدا کردن یک پیام آزاد (که قبلاً استفاده نشده)
$available = false;
foreach ($messages as $msg) {
    if (empty($msg['used'])) {
        $price = $msg['price'];
        $available = true;
        break;
    }
}

if (!$available) {
    exit('هیچ پیام آزادی موجود نیست.');
}

$order_id = 'order_' . uniqid();
$status = 'unpaid';

// ذخیره سفارش در دیتابیس
$stmt = $pdo->prepare("INSERT INTO orders (order_id, product_id, price, status) VALUES (?, ?, ?, ?)");
$stmt->execute([$order_id, $product_id, $price, $status]);

// اطلاعات برای ایجاد فاکتور NowPayments
$api_key = 'TAAF55T-8D24S1Q-HDPZD8T-RRW0T2K';
$nowpayments_url = 'https://api.nowpayments.io/v1/invoice';

$params = [
    'price_amount' => $price,
    'price_currency' => 'usd',
    'order_id' => $order_id,
    'order_description' => "خرید محصول دیجیتالی: {$product_id}",
    'ipn_callback_url' => 'https://farzad-shop.onrender.com/ipn.php',
    'success_url' => 'https://farzad-shop.onrender.com/success.php?order_id=' . $order_id,
    'cancel_url' => 'https://farzad-shop.onrender.com/cancelled.html',
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n" .
                     "x-api-key: $api_key\r\n",
        'method'  => 'POST',
        'content' => json_encode($params),
    ],
];

$context = stream_context_create($options);
$result = file_get_contents($nowpayments_url, false, $context);

if ($result === FALSE) {
    exit('خطا در برقراری ارتباط با NowPayments');
}

$response = json_decode($result, true);

if (!isset($response['invoice_url'])) {
    exit('خطا در دریافت لینک پرداخت');
}

// ریدایرکت به درگاه پرداخت
header("Location: " . $response['invoice_url']);
exit;
?>
