# Use an official PHP image with Apache
FROM php:8.3-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Set the working directory
WORKDIR /var/www/html

# Copy project files to the container
COPY . .

# Change ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chown -R www-data:www-data /var/www/html/var/log \
    && chmod -R 775 /var/www/html/var/log \
    && chmod -R 775 var/cache var/log

# Switch to the www-data user to avoid running Composer as root
USER www-data

# Install Composer dependencies without dev and optimize the autoloader
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Switch back to root user to run necessary scripts and change permissions
USER root

# Configure Apache for Symfony
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Enable required Apache modules
RUN a2enmod rewrite headers

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
