# Dockerfile pour musae (Laravel) - contourne le bug Nixpacks
# "caching_sha2_password" non supporté par mysqlnd compilé via Nix
# Utilise l'image PHP officielle, dont mysqlnd est compilé avec OpenSSL.

FROM php:8.4-cli

# Dépendances système nécessaires (build tools, libs pour extensions PHP, Node.js)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer Node.js 22 (pour npm ci / npm run build)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Extensions PHP nécessaires pour Laravel + MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring zip exif pcntl bcmath gd

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier les fichiers de dépendances d'abord (cache Docker plus efficace)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

COPY package.json package-lock.json ./
RUN npm ci

# Copier le reste du code
COPY . .

# Finaliser l'installation composer (scripts post-install, autoload)
RUN composer dump-autoload --optimize

# Build des assets front
RUN npm run build

# Lien storage + démarrage
CMD php artisan storage:link && php artisan serve --host 0.0.0.0 --port $PORT
