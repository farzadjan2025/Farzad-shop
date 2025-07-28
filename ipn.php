<?php
// فقط تست دریافت IPN
file_put_contents(__DIR__ . "/ipn_log.txt", date("Y-m-d H:i:s") . " | IPN RECEIVED\n", FILE_APPEND);
echo "✅ IPN RECEIVED";
