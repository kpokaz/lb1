# Використовуємо офіційний образ PHP з Apache
FROM php:8.2-apache

# Устанавливаем MySQL, PDO и необходимые расширения
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Копируем файлы проекта в контейнер
COPY . /var/www/html/

# Устанавливаем права доступа
RUN chown -R www-data:www-data /var/www/html