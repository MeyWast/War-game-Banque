# Utilisation de l'image officielle d'Apache
FROM php:7.4-apache

# Installation des extensions nécessaires pour PostgreSQL et d'autres outils
RUN apt-get update && apt-get install -y \
    libpq-dev \
    postgresql-client \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql xml

# Copier les fichiers de votre application dans le conteneur
COPY --chown=www-data:www-data . /var/www/html/

# Exposer le port d'Apache
EXPOSE 80