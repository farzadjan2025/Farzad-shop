<?php
$log_file = __DIR__ . "/ipn_log.txt";

if (file_exists($log_file)) {
    echo "<h2>📄 محتوای فایل لاگ IPN:</h2>";
    echo "<pre style='background:#eee;padding:10px;border-radius:8px;'>";
    echo htmlspecialchars(file_get_contents($log_file));
    echo "</pre>";
} else {
    echo "❌ فایل ipn_log.txt هنوز ساخته نشده است.";
}
?>