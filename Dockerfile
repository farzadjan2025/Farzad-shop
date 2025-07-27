# از تصویر رسمی PHP با Apache استفاده کن
FROM php:8.1-apache

# فایل‌ها را به مسیر root آپاچی کپی کن
COPY . /var/www/html/

# دسترسی فایل‌ها را درست کن (اختیاری)
RUN chown -R www-data:www-data /var/www/html

# اگر نیاز به mod_rewrite داری (مثلاً برای .htaccess)
RUN a2enmod rewrite

# پورت سرور
EXPOSE 80