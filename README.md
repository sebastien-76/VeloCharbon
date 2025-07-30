# Projet fil rouge formation dev php Simplon : Vélocharbon

Vous êtes chargés de développer un CMS (Content Management System) pour le site de l’EV5 des Hauts-de-France, une initiative liée au cyclotourisme.

## Prérequis

- Linux, MacOS, Windows
- Bash
- PHP 8
- Composer
- Symfony-cli
- Mariadb 10

## Installation

```
git clone https://github.com/sebastien-76/VeloCharbon
cd VeloCharbon
composer install
```
Créer une base de données et un utilisateur dédié pour cette base de données.

## Configuration

Créer un fichier `.env.local` à la racine du projet

```
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=123
DATABASE_URL="mysql://user:password@127.0.0.1:3306/VeloCharbon?serverVersion=mariadb-10.11.18&charset=utf8mb4"
```

Penser  à changer la variable `APP_SECRET` et les codes d'accès dans la variable `DATABASE_URL`.

**ATTENTION : `APP_SECRET` doit être une chaîne de caractère de 32 caractères en hexadécimal.**

## Migration et fixtures

Pour que l'application soit utilisable, il faut créer le schéma de la base de données et charger les données.

On utilise le script suivant :

```
./bin/dofilo.sh
```

## Utilisation
Lancer le serveur web de développement :

```
symfony serve -d
```

Puis ouvrir la page suivante : [https://localhost:8000](https://localhost:8000)
