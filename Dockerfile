FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Cache Laravel config
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

CMD php artisan serve --host=0.0.0.0 --port=$PORT
bash: FROM: command not found
bash: RUN: command not found
bash: COPY: command not found
bash: WORKDIR: command not found
bash: COPY: command not found
bash: RUN: command not found
bash: RUN: command not found
Microsoft Windows [Version 10.0.26100.4652]
(c) Microsoft Corporation. All rights reserved.

C:\xampp\htdocs\NCCHAD_PROJECT>
