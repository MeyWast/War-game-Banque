# README

## Introduction

Ce document explique comment installer, configurer et utiliser l'application bancaire ISEN Banque.

## Installation

### Prérequis

- Docker
- Docker Compose

### Étapes d'installation

1. Clonez le dépôt:
    ```bash
    git clone https://github.com/votre-repo/War-game-Banque.git
    cd War-game-Banque
    ```

2. Construisez et démarrez les conteneurs Docker:
    ```bash
    docker-compose up --build
    ```

## Fonctionnement

### Structure des fichiers

- `index.html`: Page de connexion.
- `account.html`: Page de création de compte.
- `synthese.html`: Page de synthèse du compte.
- `js/`: Contient les scripts JavaScript pour les interactions front-end.
- `php/`: Contient les scripts PHP pour les interactions back-end.
- `sql/creation.sql`: Script SQL pour créer et initialiser la base de données.

### Logique de l'application

1. **Authentification**:
    - L'utilisateur se connecte via `index.html`.
    - Les informations sont envoyées à `php/requests.php` pour vérification.

2. **Création de compte**:
    - L'utilisateur crée un compte via `account.html`.
    - Les informations sont envoyées à `php/requests.php` pour enregistrement.

3. **Synthèse du compte**:
    - L'utilisateur peut voir le solde et les transactions via `synthese.html`.
    - Les informations sont récupérées de la base de données et affichées.

### Sécurité

- Les mots de passe sont stockés en utilisant le hachage MD5.
- Les transactions sont vérifiées pour s'assurer que le solde est suffisant avant de les effectuer.

## Conclusion

Ce document fournit les informations nécessaires pour installer, configurer et comprendre le fonctionnement de l'application bancaire. Pour toute question ou problème, veuillez contacter l'équipe de développement.
