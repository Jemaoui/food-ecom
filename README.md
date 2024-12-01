Prérequis
Avant de commencer, assurez-vous que les éléments suivants sont installés sur votre machine :
•	Docker : Télécharger Docker
•	Docker Compose : Inclus avec Docker Desktop.
Installation
Suivez ces étapes pour installer et exécuter le projet en local.
1. Cloner le projet
Clonez le dépôt GitHub sur votre machine locale :
git clone https://github.com/username/ecommerce-restaurant.git
cd ecommerce-restaurant
2. Configurer l'environnement Docker
Le projet utilise Docker pour gérer les services. Le fichier docker-compose.yml est déjà configuré pour le projet.
3. Lancer les containers Docker
Pour démarrer les services Docker, exécutez la commande suivante :
docker-compose up -d
Cette commande va télécharger les images nécessaires et démarrer les containers pour les services suivants :
•	MySQL : Base de données.
•	PHPMyAdmin : Interface pour gérer la base de données.
•	Symfony : L'application Symfony (Apache).
•	Mailpit : Pour tester l'envoi d'e-mails.
4. Accéder à l'application
Une fois les services démarrés, vous pouvez accéder à votre application à partir de votre navigateur :
•	Application Symfony : http://localhost:8741
•	PHPMyAdmin : http://localhost:8080
•	Mailpit : http://localhost:8025
5. Configurer la base de données
L'application Symfony est configurée pour utiliser MySQL avec les informations définies dans le fichier .env. Vous pouvez maintenant créer les tables dans la base de données en exécutant la commande suivante dans le conteneur Symfony :
bash
Copier le code
docker exec -it ecom_symfony bash
php bin/console doctrine:schema:update –force

6. Configuration de Stripe
Pour configurer Stripe dans l'application, ajoutez votre clé secrète Stripe dans le fichier :

project\src\Service\Payement\StripeService.php : ligne 16

7. Créer un utilisateur admin
Si vous souhaitez créer un utilisateur admin pour accéder au back-office :
1.	Cliquez sur le bouton Connexion -> Créez un compte.
2.	Accédez à la base de données via PHPMyAdmin -> dans la table Users -> Modifiez son rôle en ["ROLE_ADMIN"].
________________________________________
Fonctionnalités principales
•	Catalogue de produits : Affichage des plats disponibles.
•	Détails d'un produit : Affichage complet d'un plat.
•	Panier : Ajouter des articles au panier (qui reste dans la session).
•	Commande : Valider une commande du panier une fois le paiement effectué.
•	Paiement via Stripe : Intégration du paiement Stripe pour traiter les commandes.
•	Back-office : Gestion des produits, catégories et commandes.
•	Espace client : Historique des commandes des utilisateurs connectés.
Technologies utilisées
•	Symfony 7.1
•	MySQL (via Docker)
•	PHPMyAdmin (pour la gestion de la base de données)
•	Stripe (pour le paiement en ligne)
•	Docker (pour la gestion des conteneurs)
•	Mailpit (pour tester les e-mails)
Cette architecture permet de gérer efficacement l'application e-commerce de restaurant tout en garantissant un environnement de développement et de production simplifié grâce à Docker

