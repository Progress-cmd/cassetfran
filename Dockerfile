FROM php:8.3-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    ffmpeg \
    curl \
    ca-certificates \
    python3 \
    && apt-get clean

# Installer yt-dlp
RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp \
    -o /usr/local/bin/yt-dlp \
    && chmod a+rx /usr/local/bin/yt-dlp

# Extensions PHP
RUN docker-php-ext-install pdo pdo_mysql

# Activer rewrite
RUN a2enmod rewrite

# Créer le dossier downloads et donner les droits
RUN mkdir -p /var/www/html/downloads && chown -R www-data:www-data /var/www/html/downloads

# Copier config Apache
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier les sources
COPY ./src/ /var/www/html/
