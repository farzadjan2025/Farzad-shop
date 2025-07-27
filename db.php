<?php
$url = getenv("DATABASE_URL");
if (!$url) {
    die("❌ متغیر DATABASE_URL تنظیم نشده است.");
}

$parts = parse_url($url);

$host = $parts["host"] ?? "localhost";
$port = $parts["port"] ?? "5432";  // اگر port نبود، پیش‌فرض 5432
$user = $parts["user"];
$pass = $parts["pass"];
$db   = ltrim($parts["path"], "/");

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ اتصال موفق به دیتابیس PostgreSQL";
} catch (PDOException $e) {
    die("❌ خطا در اتصال به دیتابیس: " . $e->getMessage());
}
?>
