FROM php:8.3-apache

WORKDIR /var/www/html

# Installation des dépendances système et extensions PHP utiles pour Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Active mod_rewrite pour Laravel
RUN a2enmod rewrite

# Configure Apache pour pointer vers /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Autorise les .htaccess Laravel
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copie du code
COPY . .

# Installation des dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissions Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
