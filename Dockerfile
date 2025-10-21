FROM php:8.2-apache
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    default-mysql-client \
    && docker-php-ext-install zip mysqli pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Copy application files
COPY Martes/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache virtual host
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Create MySQL initialization script
RUN echo '#!/bin/bash\n\
service mysql start\n\
mysql -u root -e "CREATE DATABASE IF NOT EXISTS stock_db;"\n\
mysql -u root -e "CREATE USER IF NOT EXISTS '\''stock_user'\''@'\''%'\'' IDENTIFIED BY '\''stock_password'\'';"\n\
mysql -u root -e "GRANT ALL PRIVILEGES ON stock_db.* TO '\''stock_user'\''@'\''%'\'';"\n\
mysql -u root -e "FLUSH PRIVILEGES;"\n\
mysql -u root stock_db < /var/www/html/AJAX/ListaOrdenarFiltrar/Tablas.sql\n\
service mysql stop' > /usr/local/bin/init-mysql.sh && chmod +x /usr/local/bin/init-mysql.sh
