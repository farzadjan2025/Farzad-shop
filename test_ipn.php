<?php
file_put_contents(__DIR__ . "/ipn_log.txt", date("Y-m-d H:i:s") . " | TEST OK\n", FILE_APPEND);
echo "✅ تست ثبت شد.";
