# BookShare

BookShare est une application web qui connecte les passionnés de lecture, encourage le partage et l'emprunt de livres, et réduit les déchets.

## Technologies utilisées

- Laravel 12 (PHP 8.2+)
- Doctrine ORM via laravel-doctrine/orm
- Blade avec deux thèmes : Front Office et Back Office
- Vite pour les assets
- Laravel Breeze pour l'authentification

## Fonctionnalités

- Gestion des livres (ajout, modification, suppression)
- Gestion des catégories
- Système d'emprunt de livres
- Interface d'administration
- Fonctionnalités IA pour la génération de résumés et de tags

## Installation

### Prérequis

- PHP 8.2 ou supérieur
- Composer
- Node.js et NPM

### Étapes d'installation

1. Cloner le dépôt
```bash
git clone <repository-url>
cd bookshare
```

2. Installer les dépendances PHP
```bash
composer install
```

3. Installer les dépendances JavaScript
```bash
npm install
```

4. Copier le fichier d'environnement
```bash
cp .env.example .env
```

5. Générer la clé d'application
```bash
php artisan key:generate
```

6. Configurer la base de données dans le fichier .env
```
DB_CONNECTION=sqlite
# Ou utiliser MySQL/PostgreSQL si nécessaire
```

7. Créer le fichier de base de données SQLite (si vous utilisez SQLite)
```bash
touch database/database.sqlite
```

8. Générer et exécuter les migrations Doctrine
```bash
php artisan doctrine:migrations:diff
php artisan doctrine:migrations:migrate
```

9. Créer un lien symbolique pour le stockage
```bash
php artisan storage:link
```

10. Compiler les assets
```bash
npm run build
```

11. Remplir la base de données avec des données de démonstration
```bash
php artisan db:seed --class=DemoSeeder
```

## Démarrage de l'application

```bash
php artisan serve
```

L'application sera accessible à l'adresse http://localhost:8000

## Comptes de démonstration

- Admin: admin@bookshare.local / password

## Fonctionnalités IA

L'application intègre des fonctionnalités d'IA pour:
- Générer automatiquement des résumés de livres
- Suggérer des tags pertinents basés sur le contenu

Pour activer ces fonctionnalités, configurez les variables d'environnement suivantes dans le fichier .env:
```
AI_PROVIDER=openai  # ou hf pour HuggingFace
AI_API_KEY=votre_clé_api
```

Si aucune clé API n'est fournie, l'application utilisera une méthode de génération de résumé simplifiée.

## Tests

Pour exécuter les tests:
```bash
php artisan test
```

## Structure du projet

- `app/Domain/Entities`: Entités Doctrine (User, Book, Category, Loan)
- `app/Domain/Repositories`: Repositories Doctrine
- `app/Http/Controllers`: Contrôleurs front et admin
- `app/Services`: Services, notamment AiNlpService
- `resources/views/front`: Templates Blade pour le front-office
- `resources/views/admin`: Templates Blade pour le back-office
