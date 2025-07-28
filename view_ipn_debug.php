<?php
$path = '/tmp/ipn_debug.txt';
if (file_exists($path)) {
    header('Content-Type: text/plain; charset=utf-8');
    readfile($path);
} else {
    echo "فایل لاگ وجود ندارد.";
}
