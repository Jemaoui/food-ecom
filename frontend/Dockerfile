# Utiliser l'image officielle de Node.js
FROM node:18-alpine

# Définir le dossier de travail
WORKDIR /app

# Copier les fichiers de configuration
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste du code source
COPY . .

# Exposer le port utilisé par Vite
EXPOSE 5173

# Commande pour démarrer l'application Vite
CMD ["npm", "run", "dev", "--", "--host"]
