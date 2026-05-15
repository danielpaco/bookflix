FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    ghostscript \
    supervisor \
    zip

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install

RUN chown -R www-data:www-data storage bootstrap/cache

RUN mkdir -p /tmp && chmod 777 /tmp

RUN chown -R www-data:www-data /var/www

RUN chmod -R 777 /var/www/storage
RUN chmod -R 777 /var/www/bootstrap/cache

CMD ["php-fpm"]