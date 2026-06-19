# Étape 1 : build Laravel + assets frontend
FROM php:8.4-apache AS builder

WORKDIR /app

# Dépendances système + extensions PHP utiles à Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
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

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node 20 depuis l'image officielle Node
COPY --from=node:20-bookworm-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:20-bookworm-slim /usr/local/bin/npm /usr/local/bin/npm
COPY --from=node:20-bookworm-slim /usr/local/bin/npx /usr/local/bin/npx
COPY --from=node:20-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules

# Copie du projet
COPY . .

# Installation dépendances PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Installation dépendances JS
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

# Build Vite
# Wayfinder pourra maintenant exécuter php artisan
RUN npm run build


# Étape 2 : image finale Apache + PHP
FROM php:8.3-apache AS production

WORKDIR /var/www/html

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

# Apache pointe vers /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Autorise les .htaccess Laravel
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Composer pour l'image finale
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copie du code complet depuis le builder
COPY --from=builder /app /var/www/html

# Nettoyage des dépendances JS inutiles en prod
RUN rm -rf node_modules

# Réinstallation propre des dépendances PHP de prod uniquement
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Permissions Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
