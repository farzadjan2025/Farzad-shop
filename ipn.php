<?php
file_put_contents(__DIR__ . "/ipn_log.txt", date("Y-m-d H:i:s") . " | RAW: " . file_get_contents("php://input") . "\n", FILE_APPEND);
echo "✅ RAW IPN Captured";
