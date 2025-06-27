FROM php:8.2-fpm-alpine

ENV COMPOSER_VERSION=2.2.6

# Installer les dépendances système nécessaires et l'extension Redis
RUN apk add --no-cache \
    libpq-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    libexif-dev \
    nodejs \
    npm \
    autoconf \
    build-base \
    icu-dev \
    mysql-client \
    && docker-php-ext-install \
        gd \
        opcache \
        pdo_mysql \
        zip \
        xml \
        bcmath \
        ftp \
        intl \
        exif \
    # Installer Redis via PECL et l'activer
    && pecl install redis && docker-php-ext-enable redis \
    # Installer Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=$COMPOSER_VERSION

COPY ./docker/php/fpm/laravel.ini /usr/local/etc/php/conf.d/laravel.ini

# Définir le répertoire de travail
WORKDIR /var/www
COPY . /var/www

# COMMANDES SPECIFIQUES AU PROJET
RUN if [ -f ./setup-docker.sh ]; then cp ./setup-docker.sh /usr/local/bin/setup.sh; fi
RUN chmod +x /usr/local/bin/setup.sh || true
RUN [ -f /usr/local/bin/setup.sh ] && /usr/local/bin/setup.sh || echo "setup.sh non trouvé, étape ignorée"

RUN chmod -R 777 /var/www/storage  \
    && chmod -R 777 /var/www/bootstrap \
    && chown -R www-data:www-data /var/www/storage \
    && chown -R www-data:www-data /var/www/bootstrap
