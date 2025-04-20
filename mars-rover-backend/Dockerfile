FROM laravelsail/php82-composer

WORKDIR /var/www/html

COPY . .

# Instalar extensiones necesarias
RUN apt-get update \
    && docker-php-ext-install pdo_mysql

RUN composer install

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]

