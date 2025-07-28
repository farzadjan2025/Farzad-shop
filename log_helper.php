<?php
// log_helper.php

function log_debug($message, $data = null) {
    $log_file = '/tmp/ipn_debug.txt'; // مسیر لاگ

    $timestamp = date("Y-m-d H:i:s");
    $entry = "[$timestamp] $message";

    if ($data !== null) {
        if (is_array($data) || is_object($data)) {
            $entry .= "\n" . print_r($data, true);
        } else {
            $entry .= " => $data";
        }
    }

    $entry .= "\n-------------------------\n";

    file_put_contents($log_file, $entry, FILE_APPEND);
}
