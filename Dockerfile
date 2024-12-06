# Utilisation de l'image officielle d'Apache
FROM php:7.4-apache

# Installation des extensions nécessaires pour PostgreSQL et d'autres outils
RUN apt-get update && apt-get install -y \
    libpq-dev \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql

# Copier les fichiers de votre application dans le conteneur
COPY . /var/www/html/

# Copier et configurer votre fichier de création de base de données
COPY sql/creation.sql /docker-entrypoint-initdb.d/

# Exposer le port d'Apache
EXPOSE 80

# Commande pour démarrer Apache
CMD ["apache2-foreground"]


