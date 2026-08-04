FROM php:8.4-fpm

# 1. Install dependencies & libicu-dev untuk intl
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
    libicu-dev \
    zip unzip git curl mariadb-client \
    # Tambahkan Node.js & NPM untuk compile Tailwind/Vite
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Install PHP extensions
RUN docker-php-ext-install pdo_mysql gd zip intl

# 3. Git Fix
RUN git config --global --add safe.directory /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html