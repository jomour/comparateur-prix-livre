<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class LocalizedRoute
{
    /**
     * Génère une URL localisée pour une route donnée
     */
    public static function url($routeName, $parameters = [], $absolute = true)
    {
        $locale = App::getLocale();
        $routeMapping = self::getRouteMapping();
        
        // Trouver la route correspondante dans la langue actuelle
        if (isset($routeMapping[$routeName]) && isset($routeMapping[$routeName][$locale])) {
            $localizedRouteName = $routeMapping[$routeName][$locale];
            
            if (Route::has($localizedRouteName)) {
                return route($localizedRouteName, $parameters, $absolute);
            }
        }
        
        // Fallback vers la route par défaut
        return route($routeName, $parameters, $absolute);
    }
    
    /**
     * Méthode de compatibilité pour l'ancienne API
     */
    public static function localized($name, $parameters = [], $absolute = true)
    {  
        return self::url($name, $parameters, $absolute);
    }
    
    /**
     * Redirige vers la version localisée d'une route
     */
    public static function redirectToLocalized($routeName, $parameters = [])
    {
        return redirect()->to(self::url($routeName, $parameters));
    }
    
    /**
     * Génère un lien localisé
     */
    public static function link($routeName, $text, $parameters = [], $attributes = [])
    {
        $url = self::url($routeName, $parameters);
        $attrString = '';
        
        foreach ($attributes as $key => $value) {
            $attrString .= " {$key}=\"{$value}\"";
        }
        
        return "<a href=\"{$url}\"{$attrString}>{$text}</a>";
    }
    
    /**
     * Obtient l'URL alternative dans l'autre langue
     */
    public static function getAlternateUrl($currentUrl = null)
    {
        if (!$currentUrl) {
            $currentUrl = request()->url();
        }
        
        $locale = App::getLocale();
        $alternateLocale = $locale === 'fr' ? 'en' : 'fr';
        
        // Mapping des URLs
        $urlMapping = [
            // Français vers Anglais
            '/fr/comparateur-prix-manga' => '/en/manga-price-comparator',
            '/fr/prix-manga' => '/en/manga-prices',
            '/fr/comparateur-prix-livres' => '/en/manga-book-price-comparison',
            '/fr/economiser-manga' => '/en/save-money-manga',
            '/fr/meilleur-prix-manga' => '/en/best-manga-price',
            '/fr/historique-recherches' => '/en/search-history',
            '/fr/historique-prix' => '/en/price-history',
            '/fr/mes-recherches' => '/en/my-searches',
            '/fr/estimation-lot-manga' => '/en/manga-lot-estimation',
            '/fr/recherche-photo' => '/en/photo-search',
            '/fr/mon-profil' => '/en/my-profile',
            
            // Anglais vers Français
            '/en/manga-price-comparator' => '/fr/comparateur-prix-manga',
            '/en/manga-prices' => '/fr/prix-manga',
            '/en/manga-book-price-comparison' => '/fr/comparateur-prix-livres',
            '/en/save-money-manga' => '/fr/economiser-manga',
            '/en/best-manga-price' => '/fr/meilleur-prix-manga',
            '/en/manga-price-checker' => '/fr/meilleur-prix-manga',
            '/en/search-history' => '/fr/historique-recherches',
            '/en/price-history' => '/fr/historique-prix',
            '/en/my-searches' => '/fr/mes-recherches',
            '/en/manga-lot-estimation' => '/fr/estimation-lot-manga',
            '/en/photo-search' => '/fr/recherche-photo',
            '/en/my-profile' => '/fr/mon-profil'
        ];
        
        // Remplacer la langue dans l'URL
        $alternateUrl = str_replace('/' . $locale . '/', '/' . $alternateLocale . '/', $currentUrl);
        
        // Remplacer les URLs localisées selon le mapping
        foreach ($urlMapping as $from => $to) {
            $alternateUrl = str_replace($from, $to, $alternateUrl);
        }
        
        return $alternateUrl;
    }
    
    /**
     * Génère les liens alternatifs pour le hreflang
     */
    public static function getAlternateLinks($currentUrl = null)
    {
        if (!$currentUrl) {
            $currentUrl = request()->url();
        }
        
        $alternateUrl = self::getAlternateUrl($currentUrl);
        $locale = App::getLocale();
        $alternateLocale = $locale === 'fr' ? 'en' : 'fr';
        
        return [
            $locale => $currentUrl,
            $alternateLocale => $alternateUrl,
            'x-default' => $locale === 'fr' ? $alternateUrl : $currentUrl
        ];
    }
    
    /**
     * Mapping des routes par langue
     */
    private static function getRouteMapping()
    {
        return [
            'price.search' => [
                'fr' => 'fr.comparateur.prix',
                'en' => 'en.manga.price.comparator'
            ],
            'price.search.submit' => [
                'fr' => 'fr.comparateur.recherche',
                'en' => 'en.manga.price.search'
            ],
            'price.historique' => [
                'fr' => 'fr.historique.recherches',
                'en' => 'en.search.history'
            ],
            'historique.show' => [
                'fr' => 'fr.historique.show',
                'en' => 'en.historique.show'
            ],
            'historique.show.lot' => [
                'fr' => 'fr.historique.show.lot',
                'en' => 'en.historique.show.lot'
            ],
            'price.verify.isbn' => [
                'fr' => 'fr.verifier.isbn',
                'en' => 'en.verify.isbn'
            ],
            'price.show.amazon' => [
                'fr' => 'fr.resultats.amazon',
                'en' => 'en.results.amazon'
            ],
            'price.show.cultura' => [
                'fr' => 'fr.resultats.cultura',
                'en' => 'en.results.cultura'
            ],
            'price.show.fnac' => [
                'fr' => 'fr.resultats.fnac',
                'en' => 'en.results.fnac'
            ],
            'manga.lot.estimation.upload.form' => [
    'fr' => 'fr.estimation.lot.manga',
    'en' => 'en.manga.lot.estimation'
],
'manga.lot.estimation.upload.form.fr' => [
    'fr' => 'fr.estimation.lot.manga',
    'en' => 'en.manga.lot.estimation'
],
'manga.lot.estimation.upload.process' => [
    'fr' => 'fr.upload.image',
    'en' => 'en.upload.image'
],
'manga.lot.estimation.upload.ajax' => [
    'fr' => 'fr.upload.image.ajax',
    'en' => 'en.upload.image.ajax'
],
'manga.lot.estimation.search.isbn' => [
    'fr' => 'fr.recherche.isbn.image',
    'en' => 'en.search.isbn.image'
],
'manga.lot.estimation.search.price' => [
    'fr' => 'fr.recherche.prix.image',
    'en' => 'en.search.price.image'
],
'manga.lot.estimation.search.all.prices' => [
    'fr' => 'fr.recherche.tous.prix',
    'en' => 'en.search.all.prices'
],
'manga.lot.estimation.update.isbn' => [
    'fr' => 'fr.mettre.a.jour.isbn',
    'en' => 'en.update.isbn'
],
'manga.lot.estimation.remove.manga' => [
    'fr' => 'fr.supprimer.manga',
    'en' => 'en.remove.manga'
],
'manga.lot.estimation.show' => [
    'fr' => 'fr.afficher.image',
    'en' => 'en.show.image'
],
'resultats.recherche.image' => [
    'fr' => 'fr.resultats.recherche.image',
    'en' => 'en.image.search.results'
],
            'profile.edit' => [
                'fr' => 'fr.mon.profil',
                'en' => 'en.my.profile'
            ],
            'profile.update' => [
                'fr' => 'fr.mon.profil.update',
                'en' => 'en.my.profile.update'
            ],
            'profile.destroy' => [
                'fr' => 'fr.mon.profil.destroy',
                'en' => 'en.my.profile.destroy'
            ],
            'login' => [
                'fr' => 'login',
                'en' => 'login'
            ],
            'logout' => [
                'fr' => 'logout',
                'en' => 'logout'
            ],
            'password.request' => [
                'fr' => 'password.request',
                'en' => 'password.request'
            ],
            'password.email' => [
                'fr' => 'password.email',
                'en' => 'password.email'
            ],
            'password.confirm' => [
                'fr' => 'password.confirm',
                'en' => 'password.confirm'
            ],
            'password.update' => [
                'fr' => 'password.update',
                'en' => 'password.update'
            ],
            'password.store' => [
                'fr' => 'password.store',
                'en' => 'password.store'
            ],
            'verification.send' => [
                'fr' => 'verification.send',
                'en' => 'verification.send'
            ],
            'register' => [
                'fr' => 'register',
                'en' => 'register'
            ]
        ];
    }
    
    /**
     * Vérifie si une route existe dans la langue actuelle
     */
    public static function hasRoute($routeName)
    {
        $locale = App::getLocale();
        $routeMapping = self::getRouteMapping();
        
        foreach ($routeMapping as $baseRoute => $routes) {
            if (isset($routes[$locale])) {
                return Route::has($routes[$locale]);
            }
        }
        
        return Route::has($routeName);
    }
    
    /**
     * Génère une URL pour changer de langue (mapping logique, pas juste le préfixe)
     */
    public static function switchLanguage($locale, $currentUrl = null)
    {
        // Si pas d'URL fournie, on prend l'URL courante
        if (!$currentUrl) {
            $currentUrl = url()->current();
        }

        // Récupère le nom de la route courante
        $currentRouteName = \Route::currentRouteName();
        $routeMapping = self::getRouteMapping();

        // Cherche la clé logique correspondant à la route courante
        $logicalKey = null;
        foreach ($routeMapping as $key => $langs) {
            if (in_array($currentRouteName, $langs)) {
                $logicalKey = $key;
                break;
            }
        }

        // Si on trouve un mapping, on génère l'URL dans l'autre langue
        if ($logicalKey && isset($routeMapping[$logicalKey][$locale])) {
            // Récupère les paramètres actuels de la route
            $params = request()->route()?->parameters() ?? [];
            return route($routeMapping[$logicalKey][$locale], $params);
        }

        // Fallback : juste remplacer le préfixe (comportement actuel)
        $supportedLocales = array_keys(config('languages.supported'));
        $pattern = '#^/(' . implode('|', $supportedLocales) . ')/#';
        return preg_replace($pattern, '/' . $locale . '/', parse_url($currentUrl, PHP_URL_PATH));
    }
    
    /**
     * Génère une liste de liens de navigation localisés
     */
    public static function getNavigationLinks()
    {
        $locale = App::getLocale();
        
        $links = [
            'fr' => [
                'comparateur' => [
                    'url' => route('fr.comparateur.prix'),
                    'text' => 'Comparateur de Prix'
                ],
                'prix_manga' => [
                    'url' => route('fr.prix.manga'),
                    'text' => 'Prix Manga'
                ],
                'economiser' => [
                    'url' => route('fr.economiser.manga'),
                    'text' => 'Économiser'
                ],
                'meilleur_prix' => [
                    'url' => route('fr.meilleur.prix'),
                    'text' => 'Meilleur Prix'
                ],
                'historique' => [
                    'url' => route('fr.historique.recherches'),
                    'text' => 'Historique'
                ],
                'recherche_image' => [
                    'url' => route('fr.recherche.image'),
                    'text' => 'Recherche par Image'
                ],
                'profil' => [
                    'url' => route('fr.mon.profil'),
                    'text' => 'Mon Profil'
                ]
            ],
            'en' => [
                'comparator' => [
                    'url' => route('en.manga.price.comparator'),
                    'text' => 'Price Comparator'
                ],
                'manga_prices' => [
                    'url' => route('en.manga.prices'),
                    'text' => 'Manga Prices'
                ],
                'save_money' => [
                    'url' => route('en.save.money.manga'),
                    'text' => 'Save Money'
                ],
                'best_price' => [
                    'url' => route('en.best.manga.price'),
                    'text' => 'Best Price'
                ],
                'history' => [
                    'url' => route('en.search.history'),
                    'text' => 'History'
                ],
                'image_search' => [
                    'url' => route('en.image.search'),
                    'text' => 'Image Search'
                ],
                'profile' => [
                    'url' => route('en.my.profile'),
                    'text' => 'My Profile'
                ]
            ]
        ];
        
        return $links[$locale] ?? $links['en'];
    }
} 