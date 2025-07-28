<?php
$logFile = __DIR__ . '/ipn_log.txt';
if (file_exists($logFile)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
} else {
    echo "❌ فایل لاگ وجود ندارد.";
}
?>
