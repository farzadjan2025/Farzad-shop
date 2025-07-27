<?php
$order_id = $_GET['order_id'] ?? '';

if (!$order_id || !file_exists("orders/$order_id.json")) {
    echo "❌ سفارش یافت نشد.";
    exit;
}

$order_data = json_decode(file_get_contents("orders/$order_id.json"), true);
$status = $order_data['payment_status'] ?? '';

if (in_array($status, ['waiting', 'confirming', 'partially_paid', 'sending', 'finished'])) {
    $product_id = $order_data['product_id'];

    $messages_file = "messages/messages_{$product_id}.json";

    if (!file_exists($messages_file)) {
        echo "❌ فایل پیام یافت نشد.";
        exit;
    }

    $messages = json_decode(file_get_contents($messages_file), true);

    $selected_index = null;
    foreach ($messages as $index => $msg) {
        if (!$msg['used']) {
            $selected_index = $index;
            break;
        }
    }

    if ($selected_index === null) {
        echo "❌ پیام‌های این محصول تمام شده‌اند.";
        exit;
    }

    $msg = $messages[$selected_index];

    echo "<div style='text-align:center; font-size:18px; line-height:2; max-width:600px; margin:auto;'>";
    echo "<h2>✅ خرید موفق!</h2>";

    if ($product_id === 'facebook_sale') {
        echo "<p>📧 ایمیل: <b>{$msg['email']}</b></p>";
        echo "<p>🔑 رمز: <b>{$msg['password']}</b></p>";
        echo "<div style='margin-top:20px; color:red; font-size:16px;'>";
        echo "نوت خیلی مهم: اگر نتوانستید  فیسبوک را باز‌ کنید نگران نباشید اگر نیاز به تاید باشد من برای شما تاید میکنم فقط به تلگرام من پیام بدهید تشکر از خرید شما";
        echo "<br>آی‌دی تلگرام 👇<br><b>@farzadshop3</b>";
        echo "</div>";
    } elseif ($product_id === 'facebook_followers') {
        echo "<p>{$msg['message']}</p>";
    } else {
        echo "<pre>";
        print_r($msg);
        echo "</pre>";
    }
    echo "</div>";

    // علامت استفاده شده
    $messages[$selected_index]['used'] = true;
    file_put_contents($messages_file, json_encode($messages));
} else {
    echo "⏳ پرداخت هنوز کامل نیست یا ناموفق بوده است.";
}