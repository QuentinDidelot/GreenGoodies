# GreenGoodies

GreenGoodies est un site e-commerce dédié à la vente de produits bio et respectueux de l'environnement. 
Le projet permet aux utilisateurs de s'inscrire, de se connecter, de parcourir les produits, de les ajouter à leur panier et de passer des commandes en toute simplicité.

## Fonctionnalités

- **Inscription et connexion** : Créez-vous un nouveau compte pour accéder à toutes nos fonctionnalités.
- **Navigation des produits** : Parcourez une large gamme de produits bio.
- **Gestion du panier** : Ajoutez des produits à votre panier, modifiez les quantités et passez des commandes.
- **Processus de commande** : Validez votre commande et effectuez le paiement via Stripe.
- **Historique des commandes** : Consultez l'historique de vos commandes dans votre compte.
- **Accéder à l'API de GreenGoodies** : Activer votre accès API dans votre espace personnel pour pouvoir accès aux données de nos produits.

## Technologies utilisées

- Symfony 6.4
- PHP 8
- Doctrine ORM
- Twig pour le templating
- Stripe pour le traitement des paiements
- Nelmio pour la documentation de l'API
- JWT pour les tokens

## Installation

1. Télécharger le projet
2. Modifier le fichier .env et renseigner vos informations de connexion à la base de données ainsi que vos clefs publique et secrète pour Stripe
3. Installez les dépendances avec `composer install`
4. Créer la base de données avec `php bin/console doctrine:database:create`
5. Appliquer les migrations avec `php bin/console doctirne:migrations:migrate`
6. Insérer les fixtures avec `php bin/console doctrine:fixtures:load`
7. Lancer le serveur avec `symfony server:start`
   
