<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class BreadcrumbService
{
    /**
     * Génère les breadcrumbs pour une page
     */
    public static function generate($page = null, $data = [])
    {
        $locale = App::getLocale();
        $baseUrl = config('app.url');
        
        $breadcrumbs = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => $locale === 'fr' ? 'Accueil' : 'Home',
                'item' => $baseUrl . '/' . $locale
            ]
        ];
        
        switch ($page) {
            case 'search':
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $locale === 'fr' ? 'Recherche de Prix' : 'Price Search',
                    'item' => $baseUrl . '/' . $locale . '/' . ($locale === 'fr' ? 'prix' : 'price')
                ];
                break;
                
            case 'results':
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $locale === 'fr' ? 'Recherche de Prix' : 'Price Search',
                    'item' => $baseUrl . '/' . $locale . '/' . ($locale === 'fr' ? 'prix' : 'price')
                ];
                
                if (isset($data['title'])) {
                    $breadcrumbs[] = [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $data['title'],
                        'item' => $baseUrl . '/' . $locale . '/' . ($locale === 'fr' ? 'prix' : 'price') . '?isbn=' . ($data['isbn'] ?? '')
                    ];
                }
                break;
                
            case 'history':
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $locale === 'fr' ? 'Historique' : 'History',
                    'item' => $baseUrl . '/' . $locale . '/' . ($locale === 'fr' ? 'historique-recherches' : 'search-history')
                ];
                break;
                
            case 'image':
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $locale === 'fr' ? 'Estimation de lot de manga' : 'Manga Lot Estimation',
                    'item' => $baseUrl . '/' . $locale . '/' . ($locale === 'fr' ? 'estimation-lot-manga' : 'manga-lot-estimation')
                ];
                break;
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        ];
    }
    
    /**
     * Génère le HTML des breadcrumbs
     */
    public static function generateHtml($page = null, $data = [])
    {
        $locale = App::getLocale();
        $breadcrumbs = [];
        
        // Accueil
        $breadcrumbs[] = [
            'name' => $locale === 'fr' ? 'Accueil' : 'Home',
            'url' => url('/' . $locale),
            'active' => false
        ];
        
        switch ($page) {
            case 'search':
                $breadcrumbs[] = [
                    'name' => $locale === 'fr' ? 'Recherche de Prix' : 'Price Search',
                    'url' => url('/' . $locale . '/' . ($locale === 'fr' ? 'prix' : 'price')),
                    'active' => true
                ];
                break;
                
            case 'results':
                $breadcrumbs[] = [
                    'name' => $locale === 'fr' ? 'Recherche de Prix' : 'Price Search',
                    'url' => url('/' . $locale . '/' . ($locale === 'fr' ? 'prix' : 'price')),
                    'active' => false
                ];
                
                if (isset($data['title'])) {
                    $breadcrumbs[] = [
                        'name' => $data['title'],
                        'url' => url('/' . $locale . '/' . ($locale === 'fr' ? 'prix' : 'price') . '?isbn=' . ($data['isbn'] ?? '')),
                        'active' => true
                    ];
                }
                break;
                
            case 'history':
                $breadcrumbs[] = [
                    'name' => $locale === 'fr' ? 'Historique' : 'History',
                    'url' => url('/' . $locale . '/' . ($locale === 'fr' ? 'historique-recherches' : 'search-history')),
                    'active' => true
                ];
                break;
                
            case 'image':
                $breadcrumbs[] = [
                    'name' => $locale === 'fr' ? 'Estimation de lot de manga' : 'Manga Lot Estimation',
                    'url' => url('/' . $locale . '/' . ($locale === 'fr' ? 'estimation-lot-manga' : 'manga-lot-estimation')),
                    'active' => true
                ];
                break;
        }
        
        return $breadcrumbs;
    }
} 