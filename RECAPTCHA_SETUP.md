# Configuration reCAPTCHA pour le Comparateur de Prix Manga

## Installation

Le système reCAPTCHA v3 a été intégré au comparateur de prix manga pour protéger les formulaires de recherche contre les abus.

## Configuration

### 1. Obtenir les clés reCAPTCHA

1. Allez sur [Google reCAPTCHA](https://www.google.com/recaptcha/admin)
2. Créez un nouveau site
3. Sélectionnez reCAPTCHA v3
4. Ajoutez vos domaines (ex: localhost, votre-domaine.com)
5. Copiez la clé du site et la clé secrète

### 2. Variables d'environnement

Ajoutez ces variables à votre fichier `.env` :

```env
RECAPTCHA_SITE_KEY=votre_cle_site_ici
RECAPTCHA_SECRET_KEY=votre_cle_secrete_ici
```

### 3. Configuration des routes

Les routes suivantes sont maintenant protégées par reCAPTCHA :

**Routes françaises :**
- `/fr/comparateur-prix-manga/recherche`
- `/fr/prix-manga/recherche`
- `/fr/verifier-isbn`

**Routes anglaises :**
- `/en/manga-price-comparator/search`
- `/en/manga-prices/search`
- `/en/verify-isbn`

## Fonctionnement

### reCAPTCHA v3 Invisible

- Le reCAPTCHA s'exécute automatiquement lors de la soumission du formulaire
- Aucune interaction utilisateur requise
- Score de confiance basé sur le comportement de l'utilisateur
- Seuil de score configuré à 0.5 (modifiable dans le middleware)

### Intégration dans les vues

Le composant `<x-recaptcha>` est automatiquement inclus dans le formulaire de recherche :

```blade
<x-recaptcha action="search" />
```

### Gestion des erreurs

Les erreurs reCAPTCHA sont affichées automatiquement :

```blade
@error('recaptcha')
    <div class="text-red-600 text-sm mt-2">
        {{ $message }}
    </div>
@enderror
```

## Personnalisation

### Modifier le seuil de score

Dans `app/Http/Middleware/RecaptchaMiddleware.php` :

```php
if (!$result['success'] || $result['score'] < 0.5) {
    // Modifier 0.5 pour un seuil différent
}
```

### Ajouter reCAPTCHA à d'autres formulaires

1. Ajouter le middleware à la route :
```php
Route::post('/votre-route', [Controller::class, 'method'])->middleware(['auth', 'recaptcha']);
```

2. Ajouter le composant à la vue :
```blade
<x-recaptcha action="votre_action" />
```

## Dépannage

### Erreurs courantes

1. **"Veuillez compléter le reCAPTCHA"**
   - Vérifiez que les clés reCAPTCHA sont correctes
   - Vérifiez que le domaine est autorisé dans la console reCAPTCHA

2. **"Vérification reCAPTCHA échouée"**
   - Le score de confiance est trop bas
   - Vérifiez les logs pour plus de détails

3. **reCAPTCHA ne se charge pas**
   - Vérifiez la clé du site dans la configuration
   - Vérifiez la connexion internet

### Mode développement

Pour les tests en local, vous pouvez temporairement désactiver reCAPTCHA en commentant le middleware dans les routes.

## Sécurité

- Les clés secrètes ne doivent jamais être exposées côté client
- Le score de confiance est vérifié côté serveur
- Les requêtes sont limitées par IP pour éviter les abus 