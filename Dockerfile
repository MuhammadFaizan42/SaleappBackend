FROM php:8.2-apache

RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y \
    unzip \
    wget \
    libaio1 \
    libaio-dev \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /tmp

# Oracle Instant Client
RUN wget https://download.oracle.com/otn_software/linux/instantclient/219000/instantclient-basiclite-linux.x64-21.9.0.0.0dbru.zip \
 && wget https://download.oracle.com/otn_software/linux/instantclient/219000/instantclient-sdk-linux.x64-21.9.0.0.0dbru.zip \
 && unzip instantclient-basiclite-linux.x64-21.9.0.0.0dbru.zip -d /opt/oracle \
 && unzip instantclient-sdk-linux.x64-21.9.0.0.0dbru.zip -d /opt/oracle \
 && rm -f instantclient-basiclite-linux.x64-21.9.0.0.0dbru.zip \
 && rm -f instantclient-sdk-linux.x64-21.9.0.0.0dbru.zip

# Add Oracle libs to system path
RUN echo "/opt/oracle/instantclient_21_9" > /etc/ld.so.conf.d/oracle-instantclient.conf \
 && ldconfig

ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_21_9
ENV ORACLE_HOME=/opt/oracle/instantclient_21_9

# Install OCI8
RUN pecl channel-update pecl.php.net \
 && echo "instantclient,/opt/oracle/instantclient_21_9" | pecl install oci8

# Enable OCI8 manually
RUN echo "extension=oci8.so" > /usr/local/etc/php/conf.d/20-oci8.ini

WORKDIR /var/www/html
COPY . /var/www/html

EXPOSE 80
