# استفاده از نسخه PHP مناسب
FROM php:8.2-cli

# نصب PostgreSQL driver (pdo_pgsql)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# انتقال فایل‌های پروژه به داخل کانتینر
COPY . /app
WORKDIR /app

# اجرای سرور PHP داخلی
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
