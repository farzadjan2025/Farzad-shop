<?php
$url = getenv("DATABASE_URL");
if (!$url) {
    die("❌ متغیر DATABASE_URL تنظیم نشده است.");
}

$parts = parse_url($url);

$host = $parts["host"] ?? "localhost";
$port = $parts["port"] ?? "5432";
$user = $parts["user"];
$pass = $parts["pass"];
$db   = ltrim($parts["path"], "/");

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ ساخت جدول orders اگر وجود نداشت
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        order_id TEXT PRIMARY KEY,
        product_id TEXT,
        price REAL,
        payment_id TEXT,
        email TEXT,
        password TEXT,
        status TEXT
    )");

} catch (PDOException $e) {
    die("❌ خطا در اتصال به دیتابیس: " . $e->getMessage());
}
?>
