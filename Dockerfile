FROM php:8.3-apache

# Dépendances
RUN apt-get update && apt-get install -y \
    ffmpeg \
    curl \
    ca-certificates \
    python3 \
    && apt-get clean

# yt-dlp
RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp \
    -o /usr/local/bin/yt-dlp \
    && chmod a+rx /usr/local/bin/yt-dlp

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Apache rewrite
RUN a2enmod rewrite

# Créer dossier downloads
RUN mkdir -p /var/www/html/downloads && chown -R www-data:www-data /var/www/html/downloads

# Copier config Apache
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Définir dossier de travail
WORKDIR /var/www/html

# Copier code
COPY ./src/ /var/www/html/
