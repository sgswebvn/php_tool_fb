git pull
composer install   # nếu có thay đổi composer.json
chown -R www-data:www-data /var/www/mutifacebook
systemctl reload nginx
