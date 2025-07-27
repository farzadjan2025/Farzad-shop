<?php
$body = file_get_contents("php://input");
file_put_contents("ipn_debug.txt", $body . "\n---\n", FILE_APPEND);
echo "✅";
?>