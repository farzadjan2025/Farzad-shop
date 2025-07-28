<?php
$testData = [
    "payment_status" => "paid",
    "order_id" => "order_688697c288c92"
];

$jsonData = json_encode($testData);

$ch = curl_init("https://farzad-shop.onrender.com/ipn.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL error: $error\n";
} else {
    echo "✅ پاسخ سرور ($httpCode):\n$response";
}
