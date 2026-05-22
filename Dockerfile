FROM php:8.2-apache

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring xml zip gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Enable Apache rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all application files FIRST (including artisan)
COPY . .

# Install PHP dependencies (skip scripts that require artisan)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --ignore-platform-req=ext-gd --no-scripts

# Now run the scripts after composer install
RUN composer run-script post-autoload-dump

# Install frontend dependencies and build assets
RUN npm install && npm run build || echo "Frontend build skipped"

# Configure Apache to use port 10000
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess for Laravel
RUN printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel

# Clear config cache (skip if artisan not available)
RUN php artisan config:clear || true && \
    php artisan route:clear || true && \
    php artisan view:clear || true

# Create storage symlink
RUN php artisan storage:link || true

# Fix permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache public/uploads && \
    chown -R www-data:www-data storage bootstrap/cache public/uploads && \
    chmod -R 775 storage bootstrap/cache public/uploads

# Expose port
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]