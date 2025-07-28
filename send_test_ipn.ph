<?php
$data = [
    "order_id" => "order_test_".uniqid(),
    "payment_status" => "paid",
    "price_amount" => 0.98,
    "pay_currency" => "usdt",
    "pay_amount" => 0.98
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents('https://farzad-shop.onrender.com/ipn.php', false, $context);
echo "نتیجه: " . $result;
?>
