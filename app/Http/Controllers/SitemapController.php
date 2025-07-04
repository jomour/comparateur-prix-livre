<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoriqueSearch;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    /**
     * Génère le sitemap principal
     */
    public function index()
    {
        $content = view('sitemap.index')->render();
        
        return response($content)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Length', strlen($content));
    }

    /**
     * Génère le sitemap XML avec toutes les URLs
     */
    public function generate()
    {
        $urls = [];
        
        // URLs statiques françaises
        $frUrls = [
            '/fr/comparateur-prix-manga',
            '/fr/prix-manga',
            '/fr/comparateur-prix-livres',
            '/fr/economiser-manga',
            '/fr/meilleur-prix-manga',
            '/fr/historique-recherches',
            '/fr/historique-prix',
            '/fr/mes-recherches',
            '/fr/recherche-image',
            '/fr/recherche-photo',
            '/fr/mon-profil'
        ];
        
        // URLs statiques anglaises
        $enUrls = [
            '/en/manga-price-comparator',
            '/en/manga-prices',
            '/en/manga-book-price-comparison',
            '/en/save-money-manga',
            '/en/best-manga-price',
            '/en/manga-price-checker',
            '/en/search-history',
            '/en/price-history',
            '/en/my-searches',
            '/en/image-search',
            '/en/photo-search',
            '/en/my-profile'
        ];
        
        // Ajouter les URLs françaises
        foreach ($frUrls as $url) {
            $urls[] = [
                'loc' => url($url),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'hreflang' => [
                    'fr' => url($url),
                    'en' => $this->getEnglishEquivalent($url),
                    'x-default' => $this->getEnglishEquivalent($url)
                ]
            ];
        }
        
        // Ajouter les URLs anglaises
        foreach ($enUrls as $url) {
            $urls[] = [
                'loc' => url($url),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'hreflang' => [
                    'fr' => $this->getFrenchEquivalent($url),
                    'en' => url($url),
                    'x-default' => url($url)
                ]
            ];
        }
        
        // Récupérer les recherches populaires pour créer des URLs dynamiques
        $popularSearches = HistoriqueSearch::select('isbn')
            ->selectRaw('COUNT(*) as search_count')
            ->groupBy('isbn')
            ->orderBy('search_count', 'desc')
            ->limit(100)
            ->get();
        
        foreach ($popularSearches as $search) {
            // URLs françaises pour les recherches populaires
            $frSearchUrl = '/fr/comparateur-prix-manga?isbn=' . $search->isbn;
            $urls[] = [
                'loc' => url($frSearchUrl),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
                'hreflang' => [
                    'fr' => url($frSearchUrl),
                    'en' => url('/en/manga-price-comparator?isbn=' . $search->isbn),
                    'x-default' => url('/en/manga-price-comparator?isbn=' . $search->isbn)
                ]
            ];
            
            // URLs anglaises pour les recherches populaires
            $enSearchUrl = '/en/manga-price-comparator?isbn=' . $search->isbn;
            $urls[] = [
                'loc' => url($enSearchUrl),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
                'hreflang' => [
                    'fr' => url('/fr/comparateur-prix-manga?isbn=' . $search->isbn),
                    'en' => url($enSearchUrl),
                    'x-default' => url($enSearchUrl)
                ]
            ];
        }
        
        $content = view('sitemap.xml', compact('urls'))->render();
        
        return response($content)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Length', strlen($content));
    }
    
    /**
     * Convertit une URL française en équivalent anglais
     */
    private function getEnglishEquivalent($frUrl)
    {
        $mapping = [
            '/fr/comparateur-prix-manga' => '/en/manga-price-comparator',
            '/fr/prix-manga' => '/en/manga-prices',
            '/fr/comparateur-prix-livres' => '/en/manga-book-price-comparison',
            '/fr/economiser-manga' => '/en/save-money-manga',
            '/fr/meilleur-prix-manga' => '/en/best-manga-price',
            '/fr/historique-recherches' => '/en/search-history',
            '/fr/historique-prix' => '/en/price-history',
            '/fr/mes-recherches' => '/en/my-searches',
            '/fr/recherche-image' => '/en/image-search',
            '/fr/recherche-photo' => '/en/photo-search',
            '/fr/mon-profil' => '/en/my-profile'
        ];
        
        return url($mapping[$frUrl] ?? '/en/manga-price-comparator');
    }
    
    /**
     * Convertit une URL anglaise en équivalent français
     */
    private function getFrenchEquivalent($enUrl)
    {
        $mapping = [
            '/en/manga-price-comparator' => '/fr/comparateur-prix-manga',
            '/en/manga-prices' => '/fr/prix-manga',
            '/en/manga-book-price-comparison' => '/fr/comparateur-prix-livres',
            '/en/save-money-manga' => '/fr/economiser-manga',
            '/en/best-manga-price' => '/fr/meilleur-prix-manga',
            '/en/manga-price-checker' => '/fr/meilleur-prix-manga',
            '/en/search-history' => '/fr/historique-recherches',
            '/en/price-history' => '/fr/historique-prix',
            '/en/my-searches' => '/fr/mes-recherches',
            '/en/image-search' => '/fr/recherche-image',
            '/en/photo-search' => '/fr/recherche-photo',
            '/en/my-profile' => '/fr/mon-profil'
        ];
        
        return url($mapping[$enUrl] ?? '/fr/comparateur-prix-manga');
    }
} 