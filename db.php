<?php
$host = 'dpg-d233n9h5pdvs739i3850-a';  // External host
$db   = 'farzad_db';
$user = 'farzad_db';
$pass = 'hO0upC4HbGjci5Ffz8KZkyW2Dv7RkPi4';
$port = '5432';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ اتصال موفق!";
} catch (PDOException $e) {
    die("❌ خطا در اتصال به دیتابیس: " . $e->getMessage());
}
?>