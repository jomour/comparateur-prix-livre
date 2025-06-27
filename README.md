# Comparateur de Prix Manga

Un comparateur de prix intelligent pour les mangas, développé avec Laravel, qui compare les prix entre Amazon, Cultura et Fnac, et fournit une estimation de prix d'occasion via OpenAI.

## 🚀 Fonctionnalités

- **Comparaison de prix** : Amazon, Cultura, Fnac
- **Estimation IA** : Prix d'occasion estimé par OpenAI GPT-4
- **Historique** : Sauvegarde de toutes les recherches
- **Interface moderne** : Design responsive avec Tailwind CSS
- **Authentification** : Système de connexion sécurisé
- **Accès restreint** : Enregistrement désactivé (accès administrateur uniquement)

## 🛠️ Technologies utilisées

- **Backend** : Laravel 12.x
- **Frontend** : Tailwind CSS, Alpine.js
- **Base de données** : MySQL
- **APIs externes** :
  - [ScraperAPI](https://www.scraperapi.com/) - Récupération des prix
  - [OpenAI GPT-4](https://openai.com/) - Estimation des prix d'occasion
  - [OpenLibrary](https://openlibrary.org/) - Récupération des titres
  - [Google Books API](https://developers.google.com/books) - Fallback pour les titres

## 📋 Prérequis

- Docker et Docker Compose
- Composer
- Node.js et npm
- Clés API :
  - ScraperAPI
  - OpenAI

## 🐳 Installation avec Docker

### 1. Cloner le repository
```bash
git clone https://github.com/jomour/comparateur-prix-livre.git
cd comparateur-prix-livre
```

### 2. Configurer les variables d'environnement
```bash
cp .env.example .env
```

Éditer le fichier `.env` et configurer :
```env
# Base de données
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=comparateur_manga
DB_USERNAME=root
DB_PASSWORD=password

# APIs
SCRAPER_API_KEY=votre_clé_scraperapi
OPENAI_API_KEY=votre_clé_openai

# Application
APP_NAME="Comparateur Prix Manga"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```

### 3. Lancer avec Docker Compose
```bash
docker-compose up -d
```

### 4. Installer les dépendances
```bash
# Installer les dépendances PHP
docker-compose exec app composer install

# Installer les dépendances Node.js
docker-compose exec app npm install
```

### 5. Configuration Laravel
```bash
# Générer la clé d'application
docker-compose exec app php artisan key:generate

# Exécuter les migrations
docker-compose exec app php artisan migrate

# Créer le lien symbolique pour le stockage
docker-compose exec app php artisan storage:link

# Compiler les assets
docker-compose exec app npm run build
```

### 6. Créer un utilisateur administrateur
```bash
docker-compose exec app php artisan tinker
```

```php
use App\Models\User;
User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now()
]);
```

## 🔑 Configuration des APIs

### ScraperAPI
1. Créer un compte sur [ScraperAPI](https://www.scraperapi.com/)
2. Récupérer votre clé API
3. Ajouter dans `.env` : `SCRAPER_API_KEY=votre_clé`

### OpenAI
1. Créer un compte sur [OpenAI](https://platform.openai.com/)
2. Générer une clé API
3. Ajouter dans `.env` : `OPENAI_API_KEY=votre_clé`

## 🚀 Utilisation

### Accès à l'application
- **URL** : http://localhost
- **Redirection automatique** vers la page de connexion
- **Se connecter** avec les identifiants créés

### Fonctionnalités
1. **Recherche** : Saisir un ISBN de manga
2. **Comparaison** : Visualiser les prix Amazon, Cultura, Fnac
3. **Estimation IA** : Voir l'estimation de prix d'occasion
4. **Historique** : Consulter toutes les recherches passées
5. **Fichiers HTML** : Accéder aux pages sources via les liens

## 📊 Structure de la base de données

### Table `historique_search`
- `id` : Identifiant unique
- `user_id` : ID de l'utilisateur
- `isbn` : ISBN du manga
- `prix_amazon` : Prix trouvé sur Amazon
- `prix_cultura` : Prix trouvé sur Cultura
- `prix_fnac` : Prix trouvé sur Fnac
- `estimation_occasion` : Estimation OpenAI
- `created_at` : Date de création

## 🔒 Sécurité

- **Authentification requise** pour toutes les fonctionnalités
- **Enregistrement désactivé** : Seuls les administrateurs peuvent créer des comptes
- **Isolation des données** : Chaque utilisateur ne voit que ses recherches
- **Protection des fichiers** : Accès restreint aux résultats HTML

## 🐛 Dépannage

### Problèmes courants

**Erreur de timeout ScraperAPI**
- Vérifier la clé API
- Augmenter le timeout dans le code si nécessaire

**Erreur OpenAI**
- Vérifier la clé API
- Vérifier les crédits disponibles

**Problèmes de base de données**
```bash
docker-compose exec app php artisan migrate:fresh
```

**Problèmes de permissions**
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

## 📝 API Endpoints

### Routes protégées (authentification requise)
- `GET /price` - Page de recherche
- `POST /price/search` - Effectuer une recherche
- `GET /price/historique` - Historique des recherches
- `GET /price/show/amazon/{id}` - Afficher fichier Amazon
- `GET /price/show/cultura/{id}` - Afficher fichier Cultura
- `GET /price/show/fnac/{id}` - Afficher fichier Fnac

## 🤝 Contribution

Ce projet est en accès restreint. Pour contribuer :
1. Contacter l'administrateur pour obtenir un accès
2. Créer une branche pour votre fonctionnalité
3. Soumettre une pull request

## 📄 Licence

Projet privé - Accès restreint

## 👥 Auteur

Développé pour la comparaison de prix de mangas avec intégration IA.

---

**Note** : Ce projet utilise des APIs payantes (ScraperAPI, OpenAI). Assurez-vous d'avoir des crédits suffisants pour le fonctionnement.
