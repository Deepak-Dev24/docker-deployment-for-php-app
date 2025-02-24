# Use official PHP image with Apache
FROM php:8.2-apache

# Install required dependencies
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory inside the container
WORKDIR /var/www/html

# Copy application files to the container
COPY . /var/www/html/

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80 for web access
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
