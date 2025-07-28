<?php
$log_path = "/tmp/ipn_log.txt";

if (!file_exists($log_path)) {
    echo "❌ فایل لاگ در /tmp پیدا نشد.";
    exit;
}

echo "<h2>📄 محتوای /tmp/ipn_log.txt</h2><pre>";
echo htmlspecialchars(file_get_contents($log_path));
echo "</pre>";
