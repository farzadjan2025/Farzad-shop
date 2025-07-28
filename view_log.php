<?php
$log_file = __DIR__ . '/ipn_log.txt';

if (!file_exists($log_file)) {
    echo "❌ فایل لاگ وجود ندارد.";
    exit;
}

$log = file_get_contents($log_file);
echo "<pre>" . htmlspecialchars($log) . "</pre>";
?>
