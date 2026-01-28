# --- ÉTAPE 1 : BASE COMMUNE ---
FROM php:8.2-apache AS base

# Installation des dépendances système et PHP
RUN apt-get update && apt-get install -y \
    python3 \
    ffmpeg \
    libmariadb-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation de yt-dlp
RUN curl -fL https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp \
    -o /usr/local/bin/yt-dlp \
    && chmod +x /usr/local/bin/yt-dlp

# Configuration Apache (Activation de mod_rewrite pour les routes propres)
RUN a2enmod rewrite headers

# Dossier pour les musiques (hors de la racine web pour la sécurité)
RUN mkdir -p /var/www/music_data && chmod -rw /var/www/music_data

WORKDIR /var/www/html

# --- ÉTAPE 2 : CONFIGURATION POUR LE DÉVELOPPEMENT ---
FROM base AS dev
# 1. On utilise le template par défaut de PHP pour le dev
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
# 2. On ajoute TES réglages de dev (erreurs affichées, etc.)
# On le nomme z-dev.ini pour qu'il soit chargé en dernier et écrase le reste
COPY ./php/php-dev.ini /usr/local/etc/php/conf.d/z-dev.ini

# --- ÉTAPE 3 : CONFIGURATION POUR LA PRODUCTION ---
FROM base AS prod
# 1. On utilise le template par défaut de PHP pour la prod (sécurisé)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# 2. On ajoute TES réglages de prod (sécurité maximale)
COPY ./php/php-prod.ini /usr/local/etc/php/conf.d/z-prod.ini

# Copier config Apache
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copie sécurisée du code source
COPY ./src /var/www/html

# Droits d'accès stricts
RUN chown -R www-data:www-data /var/www/html /var/www/music_data

# Configuration de php
COPY ./php/security.ini /usr/local/etc/php/conf.d/

USER www-data