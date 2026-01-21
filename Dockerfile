FROM php:8.2-apache

RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y \
    unzip \
    wget \
    libaio-dev \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------
# Oracle Instant Client (Basic + SDK)
# -------------------------------
WORKDIR /tmp

RUN wget https://download.oracle.com/otn_software/linux/instantclient/219000/instantclient-basiclite-linux.x64-21.9.0.0.0dbru.zip \
 && wget https://download.oracle.com/otn_software/linux/instantclient/219000/instantclient-sdk-linux.x64-21.9.0.0.0dbru.zip \
 && unzip instantclient-basiclite-linux.x64-21.9.0.0.0dbru.zip -d /opt/oracle \
 && unzip instantclient-sdk-linux.x64-21.9.0.0.0dbru.zip -d /opt/oracle \
 && rm -f instantclient-basiclite-linux.x64-21.9.0.0.0dbru.zip \
 && rm -f instantclient-sdk-linux.x64-21.9.0.0.0dbru.zip

# Set env vars
ENV ORACLE_HOME=/opt/oracle/instantclient_21_9
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_21_9

# Required symlinks
RUN ln -s /opt/oracle/instantclient_21_9/libclntsh.so.21.1 /opt/oracle/instantclient_21_9/libclntsh.so || true \
 && ln -s /opt/oracle/instantclient_21_9/libocci.so.21.1 /opt/oracle/instantclient_21_9/libocci.so || true

# -------------------------------
# Install PDO_OCI
# -------------------------------
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient_21_9,21.1 \
 && docker-php-ext-install pdo_oci

# -------------------------------
# Install OCI8 (IMPORTANT FIX)
# -------------------------------
RUN echo "instantclient,/opt/oracle/instantclient_21_9" | pecl install oci8 \
 && docker-php-ext-enable oci8

# Copy project files
WORKDIR /var/www/html
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
