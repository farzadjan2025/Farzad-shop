<?php
// دریافت آدرس دیتابیس از محیط سیستم (Render به‌طور خودکار تنظیم می‌کند)
$url = getenv("DATABASE_URL");
if (!$url) {
    die("❌ متغیر DATABASE_URL تنظیم نشده است.");
}

// تجزیه آدرس به اجزای اتصال
$parts = parse_url($url);

$host = $parts["host"];
$port = $parts["port"];
$user = $parts["user"];
$pass = $parts["pass"];
$db   = ltrim($parts["path"], "/");

try {
    // ساختن اتصال PDO به PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ اتصال موفق به دیتابیس PostgreSQL";
} catch (PDOException $e) {
    die("❌ خطا در اتصال به دیتابیس: " . $e->getMessage());
}
?>
