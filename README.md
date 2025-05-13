# Base de Connaissance

Ce projet est une application Laravel 12 utilisant Tailwind CSS v4 pour la mise en page et le style.

## Prérequis

- PHP >= 8.2
- Composer
- Node.js & npm
- Une base de données (MySQL, PostgreSQL, etc.)

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone <https://github.com/Trapo6x7/RefactoEriFinal>
   cd baseDeConnaissance
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Installer les dépendances front-end**
   ```bash
   npm install
   ```

4. **Copier le fichier d'environnement**
   ```bash
   cp .env.example .env
   ```

5. **Générer la clé d'application**
   ```bash
   php artisan key:generate
   ```
   Ou, pour l'environnement local :
   ```bash
   php artisan key:generate --env=local
   ```

6. **Configurer la base de données**

   Modifiez le fichier `.env` pour renseigner vos identifiants de base de données.  
   Pour utiliser MySQL avec ce projet, configurez les variables suivantes dans le fichier `.env` :

   ```env
   BASE_DE_DONNEES_CONNEXION=mysql
   BASE_DE_DONNEES_HOTE=127.0.0.1
   BASE_DE_DONNEES_PORT=3306
   BASE_DE_DONNEES_NOM=test_eri
   BASE_DE_DONNEES_UTILISATEUR=VotreNomUtilisateur
   BASE_DE_DONNEES_MOT_DE_PASSE=VotreMotDePasse
   ```

7. **Lancer les migrations**
   ```bash
   php artisan migrate
   ```

8. **Compiler les assets**
   ```bash
   npm run build
   ```

9. **Démarrer le serveur de développement**
   ```bash
   php artisan serve
   ```

## Stack technique

- **Laravel 12**
- **Tailwind CSS v4**
- Authentification Laravel Breeze

## Fonctionnalités

- Authentification (connexion, inscription, déconnexion)
- Tableau de bord utilisateur
- Navigation responsive

---

Pour toute question, ouvrez une issue ou contactez le mainteneur du projet.