<?php
require_once 'db.php'; // اتصال به دیتابیس، همینجا بگذار

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php'; // اتصال به دیتابیس

$api_key = '3N86QHJ-31FMW8W-GYA57QV-DW9HHYV'; // کلید API NOWPayments

$product_id = $_GET['product_id'] ?? 'facebook_sale';

if (isset($_GET['price'])) {
    $price = floatval($_GET['price']);
} else {
    $json_file = "messages/" . basename($product_id) . ".json";
    if (file_exists($json_file)) {
        $json_data = json_decode(file_get_contents($json_file), true);

        if ($json_data === null || !is_array($json_data) || count($json_data) === 0) {
            echo "❌ فایل JSON خالی یا خراب است.";
            exit;
        }

        $price = isset($json_data[0]['price']) ? floatval($json_data[0]['price']) : 1.0;
    } else {
        $price = 1.0;
    }
}

$order_id = uniqid("order_");

// ذخیره سفارش در دیتابیس
try {
    $stmt = $pdo->prepare("INSERT INTO orders (order_id, product_id, price, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$order_id, $product_id, $price]);
} catch (PDOException $e) {
    echo "❌ خطا در ذخیره سفارش در دیتابیس: " . $e->getMessage();
    exit;
}

$data = [
    "price_amount" => $price,
    "price_currency" => "USD",
    "order_id" => $order_id,
    "order_description" => "خرید محصول دیجیتالی: $product_id",
    "ipn_callback_url" => "https://farzad-shop.onrender.com/ipn.php",
"success_url" => "https://farzad-shop.onrender.com/success.php?order_id=$order_id",
"cancel_url" => "https://farzad-shop.onrender.com/cancel.php"
];

$ch = curl_init("https://api.nowpayments.io/v1/invoice");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "x-api-key: $api_key",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['invoice_url'])) {
    header("Location: " . $result['invoice_url']);
    exit;
} else {
    echo "<h3>❌ خطا در دریافت لینک پرداخت:</h3>";
    echo "<pre>HTTP Code: $httpcode\n";
    print_r($result);
    echo "</pre>";
}
?>