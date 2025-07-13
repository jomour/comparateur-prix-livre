<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * Génère les métadonnées SEO de base
     */
    public static function getBaseMeta($title = null, $description = null, $keywords = null, $image = null)
    {
        $locale = App::getLocale();
        $defaultTitle = $locale === 'fr' 
            ? 'Comparateur de Prix Manga - Trouvez les Meilleurs Prix sur Amazon, Fnac, Cultura' 
            : 'Manga Price Comparator - Find the Best Prices on Amazon, Fnac, Cultura';
        
        $defaultDescription = $locale === 'fr' 
            ? 'Comparez les prix de vos mangas préférés sur Amazon, Fnac et Cultura. Économisez sur vos achats de mangas avec notre comparateur de prix intelligent. Trouvez les meilleures offres et promotions.'
            : 'Compare prices of your favorite manga on Amazon, Fnac and Cultura. Save money on your manga purchases with our intelligent price comparator. Find the best deals and promotions.';
        
        $defaultKeywords = $locale === 'fr'
            ? 'comparateur prix manga, prix manga, amazon manga, fnac manga, cultura manga, économiser manga, meilleur prix manga, comparateur prix livre manga, prix livre manga, manga pas cher, comparateur prix fnac amazon, économiser sur manga, prix manga amazon, prix manga fnac, prix manga cultura, comparateur prix livre, manga discount, prix manga occasion, comparateur prix intelligent, manga meilleur prix'
            : 'manga price comparator, manga prices, amazon manga, fnac manga, cultura manga, save money manga, best manga price, manga book price comparison, manga book prices, cheap manga, price comparator fnac amazon, save on manga, manga amazon prices, manga fnac prices, manga cultura prices, book price comparator, manga discount, used manga prices, intelligent price comparator, manga best price, manga price checker, manga price finder';

        return [
            'title' => $title ? $title . ' - ' . $defaultTitle : $defaultTitle,
            'description' => $description ?: $defaultDescription,
            'keywords' => $keywords ?: $defaultKeywords,
            'image' => $image ?: asset('images/mangavaluecheck_logo.png'),
            'url' => request()->url(),
            'locale' => $locale,
            'locale_alternate' => $locale === 'fr' ? 'en' : 'fr',
        ];
    }

    /**
     * Génère les métadonnées pour une page de recherche
     */
    public static function getSearchMeta($isbn = null, $title = null)
    {
        $locale = App::getLocale();
        
        if ($isbn && $title) {
            $searchTitle = $locale === 'fr' 
                ? "Prix du manga {$title} (ISBN: {$isbn}) - Comparateur de Prix"
                : "Manga {$title} (ISBN: {$isbn}) Price - Price Comparator";
            
            $searchDescription = $locale === 'fr'
                ? "Comparez les prix du manga {$title} sur Amazon, Fnac et Cultura. ISBN: {$isbn}. Trouvez le meilleur prix pour ce manga et économisez sur votre achat."
                : "Compare prices for manga {$title} on Amazon, Fnac and Cultura. ISBN: {$isbn}. Find the best price for this manga and save on your purchase.";
        } else {
            $searchTitle = $locale === 'fr' 
                ? 'Comparateur de Prix Manga - Recherche et Comparaison de Prix'
                : 'Manga Price Comparator - Search and Price Comparison';
            
            $searchDescription = $locale === 'fr'
                ? 'Recherchez et comparez les prix de vos mangas préférés. Entrez un ISBN pour obtenir les prix sur Amazon, Fnac et Cultura. Économisez sur vos achats de mangas.'
                : 'Search and compare prices of your favorite manga. Enter an ISBN to get prices on Amazon, Fnac and Cultura. Save money on your manga purchases.';
        }

        return self::getBaseMeta($searchTitle, $searchDescription);
    }

    /**
     * Génère les métadonnées pour une page de résultats
     */
    public static function getResultsMeta($isbn, $title, $prices)
    {
        $locale = App::getLocale();
        
        // Trouver le prix le plus bas
        $validPrices = [];
        foreach ($prices as $provider => $price) {
            if (is_array($price)) {
                // Pour tous les providers qui retournent un tableau, utiliser le prix minimum
                if (isset($price['min'])) {
                    $validPrices[] = $price['min'];
                } elseif (isset($price['average_without_extremes'])) {
                    $validPrices[] = $price['average_without_extremes'];
                } elseif (isset($price['average'])) {
                    $validPrices[] = $price['average'];
                }
            } elseif (is_numeric($price)) {
                $validPrices[] = (float)$price;
            } elseif (is_string($price)) {
                $num = floatval(str_replace(['€', ' ', ','], ['', '', '.'], $price));
                if ($num > 0) {
                    $validPrices[] = $num;
                }
            }
        }

        $lowestPrice = !empty($validPrices) ? min($validPrices) : null;
        $priceText = $lowestPrice ? number_format($lowestPrice, 2) . '€' : '';
        
        $resultsTitle = $locale === 'fr'
            ? "Prix du manga {$title} - À partir de {$priceText} | Comparateur de Prix"
            : "Manga {$title} Price - From {$priceText} | Price Comparator";
            
        // Gérer le format d'Amazon qui peut être un tableau ou une string
        $amazonPrice = '';
        if (isset($prices['amazon'])) {
            if (is_array($prices['amazon']) && isset($prices['amazon']['formatted_min'])) {
                $amazonPrice = $prices['amazon']['formatted_min'] . '€';
            } elseif (is_string($prices['amazon'])) {
                $amazonPrice = $prices['amazon'];
            } else {
                $amazonPrice = 'Prix non disponible';
            }
        }
        
        // Gérer le format de Fnac qui peut être un tableau ou une string
        $fnacPrice = '';
        if (isset($prices['fnac'])) {
            if (is_array($prices['fnac']) && isset($prices['fnac']['formatted_min'])) {
                $fnacPrice = $prices['fnac']['formatted_min'] . '€';
            } elseif (is_array($prices['fnac']) && isset($prices['fnac']['formatted_average'])) {
                $fnacPrice = $prices['fnac']['formatted_average'] . '€';
            } elseif (is_string($prices['fnac'])) {
                $fnacPrice = $prices['fnac'];
            } else {
                $fnacPrice = 'Prix non disponible';
            }
        } else {
            $fnacPrice = 'Prix non disponible';
        }
        
        // Gérer le format de Cultura qui peut être un tableau ou une string
        $culturaPrice = '';
        if (isset($prices['cultura'])) {
            if (is_array($prices['cultura']) && isset($prices['cultura']['formatted_min'])) {
                $culturaPrice = $prices['cultura']['formatted_min'] . '€';
            } elseif (is_array($prices['cultura']) && isset($prices['cultura']['formatted_average'])) {
                $culturaPrice = $prices['cultura']['formatted_average'] . '€';
            } elseif (is_string($prices['cultura'])) {
                $culturaPrice = $prices['cultura'];
            } else {
                $culturaPrice = 'Prix non disponible';
            }
        } else {
            $culturaPrice = 'Prix non disponible';
        }
        
        $resultsDescription = $locale === 'fr'
            ? "Prix du manga {$title} (ISBN: {$isbn}) : Amazon {$amazonPrice}, Fnac {$fnacPrice}, Cultura {$culturaPrice}. Comparez et économisez sur votre achat !"
            : "Manga {$title} (ISBN: {$isbn}) prices: Amazon {$amazonPrice}, Fnac {$fnacPrice}, Cultura {$culturaPrice}. Compare and save on your purchase!";

        return self::getBaseMeta($resultsTitle, $resultsDescription);
    }

    /**
     * Génère les métadonnées pour la page historique
     */
    public static function getHistoryMeta()
    {
        $locale = App::getLocale();
        
        $historyTitle = $locale === 'fr' 
            ? 'Historique des Recherches - Comparateur de Prix Manga'
            : 'Search History - Manga Price Comparator';
            
        $historyDescription = $locale === 'fr'
            ? 'Consultez votre historique de recherches de prix manga. Retrouvez vos comparaisons précédentes et les meilleurs prix trouvés. Gardez une trace de vos économies.'
            : 'View your manga price search history. Find your previous comparisons and the best prices found. Keep track of your savings.';

        return self::getBaseMeta($historyTitle, $historyDescription);
    }

    /**
     * Génère les métadonnées pour la page d'estimation de lot de manga
     */
    public static function getMangaLotEstimationMeta()
    {
        $locale = App::getLocale();
        
        $uploadTitle = $locale === 'fr' 
            ? 'Estimation de Lot de Manga - Comparateur de Prix Manga'
            : 'Manga Lot Estimation - Manga Price Comparator';
            
        $uploadDescription = $locale === 'fr'
            ? 'Recherchez des mangas par image. Uploadez une photo de couverture pour identifier le manga et comparer les prix automatiquement. Recherche intelligente par reconnaissance d\'image.'
            : 'Search manga by image. Upload a cover photo to identify the manga and compare prices automatically. Intelligent search through image recognition.';

        return self::getBaseMeta($uploadTitle, $uploadDescription);
    }

    /**
     * Génère les métadonnées pour les pages de mots-clés spécifiques
     */
    public static function getKeywordSpecificMeta($keyword)
    {
        $locale = App::getLocale();
        
        $keywordMap = [
            // Français
            'comparateur-prix-livres' => [
                'title' => 'Comparateur de Prix Livres - Manga, BD, Romans',
                'description' => 'Comparez les prix de tous vos livres : mangas, bandes dessinées, romans. Trouvez les meilleurs prix sur Amazon, Fnac et Cultura.',
                'keywords' => 'comparateur prix livres, prix livres, comparateur prix bd, prix manga, comparateur prix roman, livre pas cher, prix livre amazon, prix livre fnac'
            ],
            'economiser-manga' => [
                'title' => 'Économiser sur les Mangas - Comparateur de Prix Intelligent',
                'description' => 'Économisez sur vos achats de mangas avec notre comparateur de prix. Trouvez les meilleures offres et promotions sur Amazon, Fnac et Cultura.',
                'keywords' => 'économiser manga, manga pas cher, économiser sur manga, promo manga, réduction manga, manga discount, économiser livre manga'
            ],
            'meilleur-prix-manga' => [
                'title' => 'Meilleur Prix Manga - Comparateur de Prix en Temps Réel',
                'description' => 'Trouvez le meilleur prix pour vos mangas préférés. Comparateur de prix en temps réel sur Amazon, Fnac et Cultura.',
                'keywords' => 'meilleur prix manga, prix manga, comparateur prix manga, manga moins cher, prix manga amazon, prix manga fnac'
            ],
            
            // Anglais
            'manga-book-price-comparison' => [
                'title' => 'Manga Book Price Comparison - Compare Prices Across Stores',
                'description' => 'Compare manga book prices across Amazon, Fnac and Cultura. Find the best deals and save money on your manga purchases.',
                'keywords' => 'manga book price comparison, manga book prices, book price comparison, cheap manga books, manga book deals'
            ],
            'save-money-manga' => [
                'title' => 'Save Money on Manga - Intelligent Price Comparator',
                'description' => 'Save money on your manga purchases with our intelligent price comparator. Find the best deals and promotions on Amazon, Fnac and Cultura.',
                'keywords' => 'save money manga, cheap manga, save on manga, manga deals, manga discounts, manga promotions'
            ],
            'best-manga-price' => [
                'title' => 'Best Manga Price - Real-time Price Comparator',
                'description' => 'Find the best price for your favorite manga. Real-time price comparator across Amazon, Fnac and Cultura.',
                'keywords' => 'best manga price, manga prices, manga price comparator, cheapest manga, manga amazon prices, manga fnac prices'
            ],
            'manga-price-checker' => [
                'title' => 'Manga Price Checker - Check Prices Instantly',
                'description' => 'Check manga prices instantly across multiple stores. Compare prices on Amazon, Fnac and Cultura to find the best deals.',
                'keywords' => 'manga price checker, check manga prices, manga price finder, manga price comparison tool'
            ]
        ];
        
        $meta = $keywordMap[$keyword] ?? null;
        
        if ($meta) {
            return self::getBaseMeta($meta['title'], $meta['description'], $meta['keywords']);
        }
        
        return self::getBaseMeta();
    }

    /**
     * Génère le JSON-LD structured data
     */
    public static function getStructuredData($type = 'website', $data = [])
    {
        $baseData = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => config('app.name'),
            'url' => request()->url(),
            'description' => self::getBaseMeta()['description'],
            'inLanguage' => App::getLocale(),
        ];

        if ($type === 'website') {
            $baseData['potentialAction'] = [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/' . App::getLocale() . '/' . (App::getLocale() === 'fr' ? 'comparateur-prix-manga' : 'manga-price-comparator'))
                ],
                'query-input' => 'required name=isbn'
            ];
        }

        if ($type === 'product' && isset($data['title']) && isset($data['isbn'])) {
            $baseData = array_merge($baseData, [
                'name' => $data['title'],
                'isbn' => $data['isbn'],
                'category' => 'Manga',
                'brand' => [
                    '@type' => 'Brand',
                    'name' => 'Manga'
                ]
            ]);

            if (isset($data['prices']) && !empty($data['prices'])) {
                $offers = [];
                $validPrices = [];
                
                // Extraire les prix valides (gérer les PriceStats et les strings)
                foreach ($data['prices'] as $price) {
                    if ($price instanceof \App\ValueObjects\PriceStats) {
                        // Pour les PriceStats, utiliser le prix minimum
                        if ($price->min > 0) {
                            $validPrices[] = $price->min;
                        }
                    } elseif (is_string($price) && $price !== 'Prix non trouvé' && $price !== 'Prix neuf non trouvé') {
                        // Pour les strings, extraire le prix numérique
                        $numericPrice = (float) str_replace(['€', ' ', ','], ['', '', '.'], $price);
                        if ($numericPrice > 0) {
                            $validPrices[] = $numericPrice;
                        }
                    }
                }

                if (!empty($validPrices)) {
                    $lowestPrice = min($validPrices);
                    $offers[] = [
                        '@type' => 'Offer',
                        'price' => $lowestPrice,
                        'priceCurrency' => 'EUR',
                        'availability' => 'https://schema.org/InStock',
                        'seller' => [
                            '@type' => 'Organization',
                            'name' => 'Multiple Retailers'
                        ]
                    ];
                }

                if (!empty($offers)) {
                    $baseData['offers'] = $offers;
                }
            }
        }

        return $baseData;
    }

    /**
     * Génère les liens alternatifs pour le hreflang
     */
    public static function getAlternateLinks()
    {
        $currentUrl = request()->url();
        $currentPath = request()->path();
        
        // Mapping des URLs françaises vers anglaises
        $urlMapping = [
            'comparateur-prix-manga' => 'manga-price-comparator',
            'prix-manga' => 'manga-prices',
            'comparateur-prix-livres' => 'manga-book-price-comparison',
            'economiser-manga' => 'save-money-manga',
            'meilleur-prix-manga' => 'best-manga-price',
            'historique-recherches' => 'search-history',
            'historique-prix' => 'price-history',
            'mes-recherches' => 'my-searches',
            'estimation-lot-manga' => 'manga-lot-estimation',
            'recherche-photo' => 'photo-search',
            'mon-profil' => 'my-profile',
            'resultats' => 'results',
            'recherche' => 'search'
        ];
        
        // Remplacer la langue dans l'URL
        $frUrl = str_replace('/en/', '/fr/', $currentUrl);
        $enUrl = str_replace('/fr/', '/en/', $currentUrl);
        
        // Remplacer les URLs localisées selon le mapping
        foreach ($urlMapping as $frPath => $enPath) {
            $frUrl = str_replace('/' . $enPath . '/', '/' . $frPath . '/', $frUrl);
            $enUrl = str_replace('/' . $frPath . '/', '/' . $enPath . '/', $enUrl);
        }
        
        return [
            'fr' => $frUrl,
            'en' => $enUrl,
            'x-default' => $enUrl // URL par défaut
        ];
    }

    /**
     * Génère les métadonnées Open Graph
     */
    public static function getOpenGraphMeta($meta)
    {
        return [
            'og:title' => $meta['title'],
            'og:description' => $meta['description'],
            'og:image' => $meta['image'],
            'og:url' => $meta['url'],
            'og:type' => 'website',
            'og:locale' => $meta['locale'],
            'og:locale:alternate' => $meta['locale_alternate'],
            'og:site_name' => config('app.name'),
        ];
    }

    /**
     * Génère les métadonnées Twitter Card
     */
    public static function getTwitterMeta($meta)
    {
        return [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $meta['title'],
            'twitter:description' => $meta['description'],
            'twitter:image' => $meta['image'],
            'twitter:url' => $meta['url'],
        ];
    }
} 