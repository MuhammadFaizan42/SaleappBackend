FROM php:8.2-apache

# Enable apache modules
RUN a2enmod rewrite headers

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    libaio1 \
    wget \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------
# Install Oracle Instant Client
# -------------------------------
WORKDIR /tmp

RUN wget https://download.oracle.com/otn_software/linux/instantclient/219000/instantclient-basiclite-linux.x64-21.9.0.0.0dbru.zip \
 && unzip instantclient-basiclite-linux.x64-21.9.0.0.0dbru.zip -d /opt/oracle \
 && rm -f instantclient-basiclite-linux.x64-21.9.0.0.0dbru.zip

ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_21_9

# -------------------------------
# Install OCI8 + PDO_OCI
# -------------------------------
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient_21_9,21.1 \
 && docker-php-ext-install pdo_oci

RUN pecl install oci8 \
 && docker-php-ext-enable oci8

# Copy project files
WORKDIR /var/www/html
COPY . /var/www/html

# Permissions (optional)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
