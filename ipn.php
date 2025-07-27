<?php
require 'db.php'; // اتصال به دیتابیس

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$order_id = $data['order_id'] ?? null;
$status = $data['payment_status'] ?? null;

if ($order_id && in_array($status, ['confirmed', 'finished'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE order_id = ?");
    $stmt->execute([$order_id]);
    http_response_code(200);
    echo "✅ Payment updated to PAID.";
} else {
    http_response_code(400);
    echo "❌ Invalid or incomplete data.";
}
?>