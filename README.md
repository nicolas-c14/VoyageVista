# VoyageVista - Configuration de la Base de Données

Ce guide explique comment créer la base de données à partir de zéro pour une nouvelle installation locale.

## Prérequis

- MAMP installé et en cours d'exécution
- Apache démarré
- MySQL démarré
- phpMyAdmin disponible à l'adresse `http://localhost:8888/phpMyAdmin/` ou l'URL MAMP configurée sur votre machine

## Créer la base de données à partir de zéro

1. Ouvrez phpMyAdmin.
2. Cliquez sur `Nouvelle` (New).
3. Créez une base de données nommée `voyagevista`.
4. Choisissez `utf8` (ou `utf8mb4_general_ci`) comme interclassement (collation) si phpMyAdmin le demande.
5. Ouvrez la nouvelle base de données `voyagevista`.
6. Cliquez sur l'onglet `Importer` (Import).
7. Sélectionnez le fichier `sql/voyagevista.sql` depuis ce projet.
8. Cliquez sur `Importer` / `Exécuter` (Go / Execute).

## Ce que l'import SQL crée

Le fichier d'importation crée les tables suivantes :

- `users`
- `destinations`
- `hotels`
- `reservations`

Il ajoute également les clés étrangères et les données utilisateurs de test nécessaires au bon fonctionnement de l'application.

## Connexion locale à la base de données

L'application est configurée pour utiliser ces valeurs par défaut dans `config/database.php` :

- host : `localhost`
- database : `voyagevista`
- user : `root`
- password : `root`

Si votre compte MySQL local est différent, mettez à jour le fichier `config/database.php`.

## Vérifier la configuration

Après l'importation, vérifiez que ces tables existent bien dans phpMyAdmin :

- `users`
- `destinations`
- `hotels`
- `reservations`

Ensuite, ouvrez le site et testez les pages suivantes :

- `index.php`
- `login.php`
- `admin/add-destination.php`
- `reservations/my-reservations.php`

## Si la base de données existe déjà

Si vous avez déjà importé une ancienne version de la base de données, utilisez le fichier `sql/migrate_existing_database.sql` au lieu de tout réimporter.