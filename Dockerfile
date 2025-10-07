# ==========================================================
# 🧱 Étape 1 : Build du frontend (Webpack Encore / Vue)
# ==========================================================
FROM node:18 AS build-frontend

WORKDIR /app

# Copier fichiers frontend et config
COPY package*.json webpack.config.js ./
COPY assets ./assets

# Installer dépendances et build frontend
RUN npm install
RUN npm run build

# ==========================================================
# 🐘 Étape 2 : Backend Symfony + Apache + PHP
# ==========================================================
FROM php:8.3-apache

# Installer dépendances système et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libpq-dev libzip-dev zip \
    && docker-php-ext-install intl pdo pdo_mysql pdo_pgsql opcache zip

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configurer le dossier de travail
WORKDIR /var/www/html

# Copier tout le code source
COPY . .

# SOLUTION : Remplacer index.php par l'ancien format Symfony éprouvé
RUN rm -f public/index.php
RUN echo '<?php' > public/index.php
RUN echo '' >> public/index.php
RUN echo 'use App\Kernel;' >> public/index.php
RUN echo 'use Symfony\Component\HttpFoundation\Request;' >> public/index.php
RUN echo '' >> public/index.php
RUN echo 'require_once dirname(__DIR__)."/vendor/autoload.php";' >> public/index.php
RUN echo '' >> public/index.php
RUN echo '$env = $_SERVER["APP_ENV"] ?? "prod";' >> public/index.php
RUN echo '$debug = (bool) ($_SERVER["APP_DEBUG"] ?? ("prod" !== $env));' >> public/index.php
RUN echo '' >> public/index.php
RUN echo 'if ($debug) {' >> public/index.php
RUN echo '    umask(0000);' >> public/index.php
RUN echo '}' >> public/index.php
RUN echo '' >> public/index.php
RUN echo '$kernel = new Kernel($env, $debug);' >> public/index.php
RUN echo '$request = Request::createFromGlobals();' >> public/index.php
RUN echo '$response = $kernel->handle($request);' >> public/index.php
RUN echo '$response->send();' >> public/index.php
RUN echo '$kernel->terminate($request, $response);' >> public/index.php

# Copier le build frontend
COPY --from=build-frontend /app/public/build ./public/build

# Ajouter le repo Git comme safe pour éviter les warnings
RUN git config --global --add safe.directory /var/www/html

# Installer les dépendances en mode production
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Créer la structure de dossiers nécessaire
RUN mkdir -p var/cache var/log

# Configurer Apache pour que le DocumentRoot pointe sur /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Fixer les permissions correctes pour Apache
RUN chown -R www-data:www-data /var/www/html
RUN find /var/www/html -type d -exec chmod 755 {} \;
RUN find /var/www/html -type f -exec chmod 644 {} \;
RUN chmod -R 775 var/

# Exposer le port web
EXPOSE 80

# Variables d'environnement par défaut
ENV DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Commande de démarrage
CMD ["apache2-foreground"]