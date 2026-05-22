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
    && docker-php-ext-install pdo_mysql mbstring xml zip gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Make Apache use port 10000 (Render default)
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' /etc/apache2/sites-available/000-default.conf

# Set Laravel public as document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Allow .htaccess for Laravel
RUN printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel

# Install Node.js 18 (LTS, more stable)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (with more memory and ignoring platform requirements)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --ignore-platform-req=ext-gd

# Copy the rest of the application
COPY . .

# Install frontend dependencies and build assets (with increased memory)
RUN npm install && npm run build || echo "Frontend build skipped"

# Clear config cache
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Create storage symlink (ignore if fails)
RUN php artisan storage:link || true

# Fix permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache public/uploads && \
    chown -R www-data:www-data storage bootstrap/cache public/uploads && \
    chmod -R 775 storage bootstrap/cache public/uploads

# Expose port
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]