# Utiliser une image Node.js officielle comme image de base
FROM node:14

# Définir le répertoire de travail dans le conteneur
WORKDIR /app

# Copier package.json et package-lock.json
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste des fichiers de l'application
COPY . .

# Exposer le port sur lequel l'application va s'exécuter
EXPOSE 3000

# Démarrer l'application
CMD ["node", "js/server.js"]