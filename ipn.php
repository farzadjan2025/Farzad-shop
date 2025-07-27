<?php
// بررسی امنیتی IPN با استفاده از secret
$ipn_secret = 'clwtzUdz+DdcLtFTSLZGJBcQ5wtk2iax';  // IPN Secret واقعی که در سایت تنظیم کردی
$received_hmac = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'] ?? '';
$body = file_get_contents('php://input');
$calculated_hmac = hash_hmac('sha512', $body, $ipn_secret);

// اگر امضا درست نبود، رد کن
if ($received_hmac !== $calculated_hmac) {
    http_response_code(403);
    echo "❌ IPN secret mismatch.";
    exit;
}

// اگر امضا درست بود، پردازش کن
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode($body, true);

    if (isset($data['order_id']) && isset($data['payment_status'])) {
        $order_id = $data['order_id'];
        $status = $data['payment_status'];

        $order_file = __DIR__ . "/orders/$order_id.json";
        if (file_exists($order_file)) {
            $order_data = json_decode(file_get_contents($order_file), true);
            $order_data['payment_status'] = $status;
            file_put_contents($order_file, json_encode($order_data));
        }
    }
file_put_contents('ipn_log.txt', date('Y-m-d H:i:s') . " - IPN: $order_id - Status: $status\n", FILE_APPEND);

    http_response_code(200);
    echo "✅ IPN Received";
}
?>