<?php
require 'db.php';

$product_id = $_POST['product_id'] ?? '';
if (!$product_id) exit('شناسه محصول نامعتبر است.');

$json_path = __DIR__ . "/messages/{$product_id}.json";
if (!file_exists($json_path)) exit('فایل پیام وجود ندارد.');

$messages = json_decode(file_get_contents($json_path), true);
$first = null;
foreach ($messages as $msg) {
    if (!$msg['used']) {
        $first = $msg;
        break;
    }
}

if (!$first) exit('پیام آزاد موجود نیست.');

$price = floatval($first['price']);
$order_id = 'order_' . uniqid();

$stmt = $pdo->prepare("INSERT INTO orders (order_id, product_id, price, status) VALUES (?, ?, ?, 'unpaid')");
$stmt->execute([$order_id, $product_id, $price]);

// ارسال به NowPayments
$data = [
    'price_amount' => $price,
    'price_currency' => 'usd',
    'pay_currency' => 'usdttrc20', // قابل تنظیم
    'order_id' => $order_id,
    'order_description' => "خرید محصول دیجیتالی: $product_id",
    'ipn_callback_url' => 'https://farzad-shop.onrender.com/ipn.php',
    'success_url' => "https://farzad-shop.onrender.com/success.php?order_id=$order_id",
    'cancel_url' => 'https://farzad-shop.onrender.com/cancel.php'
];

$ch = curl_init('https://api.nowpayments.io/v1/invoice');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'x-api-key: TAAF55T-8D24S1Q-HDPZD8T-RRW0T2K',
        'Content-Type: application/json',
        'X-Nowpayments-IPN-Signature: Sug/qfzKLqbKx/SFWrlIMLzofCQ4kAqe'
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
if (!isset($result['invoice_url'])) {
    exit("خطا در ساخت فاکتور");
}

header("Location: " . $result['invoice_url']);
exit;
?>
