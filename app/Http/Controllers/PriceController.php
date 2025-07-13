<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\IsbnService;
use App\Services\SeoService;
use App\Services\AnilistService;
use App\Models\HistoriqueSearch;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\PriceParserInterface;
use App\ValueObjects\PriceStats;
use App\Models\HistoriqueSearchProvider;
class PriceController extends Controller
{
    protected $amazonPriceParser;
    protected $culturaPriceParser;
    protected $fnacPriceParser;
    protected $isbnService;
    protected $anilistService;

    public function __construct(
        IsbnService $isbnService,
        AnilistService $anilistService
    ) {
        $this->amazonPriceParser = app(PriceParserInterface::class . '.amazon');
        $this->culturaPriceParser = app(PriceParserInterface::class . '.cultura');
        $this->fnacPriceParser = app(PriceParserInterface::class . '.fnac');
        $this->isbnService = $isbnService;
        $this->anilistService = $anilistService;
    }

    public function index(Request $request)
    {
        $isbn = $request->query('isbn');
        
        // Détecter le type de route pour les métadonnées SEO
        $currentPath = $request->path();
        $keyword = $this->extractKeywordFromPath($currentPath);
        
        // Métadonnées SEO selon le type de route
        if ($keyword) {
            $meta = SeoService::getKeywordSpecificMeta($keyword);
        } else {
            $meta = SeoService::getSearchMeta($isbn);
        }
        
        $seoType = 'website';
        
        return view('price.search', compact('isbn', 'meta', 'seoType'));
    }

    /**
     * Extrait le mot-clé SEO de l'URL
     */
    private function extractKeywordFromPath($path)
    {
        $pathSegments = explode('/', $path);
        $lastSegment = end($pathSegments);
        
        // Mapping des segments vers les mots-clés
        $keywordMap = [
            // Français
            'comparateur-prix-manga' => 'comparateur-prix-manga',
            'prix-manga' => 'prix-manga',
            'comparateur-prix-livres' => 'comparateur-prix-livres',
            'economiser-manga' => 'economiser-manga',
            'meilleur-prix-manga' => 'meilleur-prix-manga',
            
            // Anglais
            'manga-price-comparator' => 'manga-price-comparator',
            'manga-prices' => 'manga-prices',
            'manga-book-price-comparison' => 'manga-book-price-comparison',
            'save-money-manga' => 'save-money-manga',
            'best-manga-price' => 'best-manga-price',
            'manga-price-checker' => 'manga-price-checker'
        ];
        
        return $keywordMap[$lastSegment] ?? null;
    }

    public function search(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|max:20',
        ]);

        $isbn = $this->isbnService->cleanIsbn($request->input('isbn'));
        
        // Créer l'enregistrement de recherche
        $search = new HistoriqueSearch();
        $search->user_id = Auth::check() ? Auth::id() : null;
        $search->isbn = $isbn;
        $search->save();

        // Récupérer le titre via l'API OpenLibrary
        $title = $this->isbnService->getTitleFromIsbn($isbn);
        
        // Mettre à jour le titre
        $search->title = $title;
        $search->save();

        // Créer le dossier storage/app/public/results s'il n'existe pas
        $resultsPath = storage_path('app/public/results');
        if (!file_exists($resultsPath)) {
            mkdir($resultsPath, 0755, true);
        }

        // Récupérer les prix et résultats
        $searchId = $search->getKey();
        $searchData = $this->searchPrices($isbn, $title, $searchId, $resultsPath);

        foreach (['amazon', 'cultura', 'fnac'] as $provider) {
            $data = $searchData['prices'][$provider] ?? null;
            if (is_array($data) && isset($data['min'], $data['max'], $data['amplitude'], $data['average'], $data['count'])) {
                HistoriqueSearchProvider::create([
                    'historique_search_id' => $search->id,
                    'name' => $provider,
                    'min' => $data['min'],
                    'max' => $data['max'],
                    'amplitude' => $data['amplitude'],
                    'average' => $data['average'],
                    'nb_offre' => $data['count'],
                ]);
            }
        }

        // Métadonnées SEO pour les résultats
        $meta = SeoService::getResultsMeta($isbn, $title, $searchData['prices']);
        $seoType = 'product';
        $structuredData = SeoService::getStructuredData('product', [
            'title' => $title,
            'isbn' => $isbn,
            'prices' => $searchData['prices']
        ]);

        // Appel à AniList pour récupérer la popularité et les notes
        $popularity = $this->anilistService->getMangaPopularity($title, $isbn);

        // Appel à OpenAI pour définir la rareté du manga (avec les données de popularité)
        $rarity = $this->getMangaRarityFromOpenAI($title, $isbn, $searchData['prices'], $popularity);
        
        // Sauvegarder les données d'estimation et d'analyse
        $this->saveEstimationData($search, $rarity, $popularity, $searchData['occasion_price']);
        
        return view('price.results', [
            'isbn' => $isbn,
            'title' => $title,
            'results' => $searchData['results'],
            'prices' => $searchData['prices'],
            'historique_id' => $search->getKey(),
            'rarity' => $rarity,
            'popularity' => $popularity,
            'meta' => $meta,
            'seoType' => $seoType,
            'structuredData' => $structuredData
        ]);
    }

    private function searchPrices($isbn, $title, $searchId = null, $resultsPath = null)
    {
        $results = [];
        $prices = [];

        // AMAZON
        $amazonStats = $this->amazonPriceParser->search($isbn, $resultsPath, $searchId);
        $prices['amazon'] = $amazonStats instanceof PriceStats ? $amazonStats->toArray() : $amazonStats;
        
        // CULTURA
        $culturaStats = $this->culturaPriceParser->search($isbn, $resultsPath, $searchId);
        $prices['cultura'] = $culturaStats instanceof PriceStats ? $culturaStats->toArray() : $culturaStats;
        
        // FNAC
        $fnacStats = $this->fnacPriceParser->search($title, $resultsPath, $searchId);
        $prices['fnac'] = $fnacStats instanceof PriceStats ? $fnacStats->toArray() : $fnacStats;

        // Obtenir l'estimation de prix d'occasion
        $occasionPrice = $this->getOccasionPriceEstimation($isbn, $prices);
        return [
            'results' => $results,
            'prices' => $prices,
            'occasion_price' => $occasionPrice
        ];
    }

    private function saveEstimationData($search, $rarity, $popularity, $occasionPrice)
    {
        // Extraire les valeurs numériques des estimations d'occasion
        $correctPrice = $this->extractPriceFromString($rarity['value_estimation']['correct']);
        $bonPrice = $this->extractPriceFromString($rarity['value_estimation']['bon']);
        $excellentPrice = $this->extractPriceFromString($rarity['value_estimation']['excellent']);
        
        // Mettre à jour les données d'estimation
        $search->estimation_occasion_correct = $correctPrice;
        $search->estimation_occasion_bon = $bonPrice;
        $search->estimation_occasion_excellent = $excellentPrice;
        $search->rarete = $rarity['explanation'];
        $search->score_rarete = $rarity['score'];
        
        // Sauvegarder les données AniList
        if ($popularity['success']) {
            $search->anilist_popularite = $popularity['popularity_score'];
            $search->anilist_note = $popularity['rating'];
            $search->anilist_statut = $popularity['status'];
        }
        
        $search->save();
        
        // Sauvegarder les facteurs de rareté
        if (isset($rarity['factors']) && is_array($rarity['factors'])) {
            foreach ($rarity['factors'] as $factor) {
                \App\Models\HistoriqueSearchRarityFactor::create([
                    'historique_search_id' => $search->id,
                    'factor' => $factor
                ]);
            }
        }
    }

    private function extractPriceFromString($priceString)
    {
        if (is_numeric($priceString)) {
            return (float)$priceString;
        }
        
        // Extraire le prix d'une chaîne comme "12.50€" ou "Non disponible"
        $price = preg_replace('/[^0-9.]/', '', $priceString);
        
        if (is_numeric($price) && $price > 0) {
            return (float)$price;
        }
        
        return null;
    }

    private function getOccasionPriceEstimation($isbn, $prices = [])
    {
        try {
            // S'assurer que prices est un tableau
            if (!is_array($prices)) {
                $prices = [];
            }
            
            // Préparer les prix neufs pour le prompt avec la nouvelle structure
            $prixNeufs = [];
            $prixOccasion = [];
            
            // Traiter Amazon (peut être array ou string)
            if (isset($prices['amazon'])) {
                if (is_array($prices['amazon']) && isset($prices['amazon']['formatted_min']) && !empty($prices['amazon']['formatted_min'])) {
                    $prixOccasion[] = "Amazon occasion: " . $prices['amazon']['formatted_min'] . "€ (moyenne: " . $prices['amazon']['formatted_average'] . "€, " . $prices['amazon']['count'] . " offres)";
                } elseif (is_string($prices['amazon']) && $prices['amazon'] !== 'Prix non trouvé') {
                    $prixNeufs[] = "Amazon: " . $prices['amazon'];
                }
            }
            
            // Traiter Cultura (string simple)
            if (isset($prices['cultura']) && is_string($prices['cultura']) && $prices['cultura'] !== 'Prix neuf non trouvé') {
                $prixNeufs[] = "Cultura: " . $prices['cultura'];
            }
            
            // Traiter Fnac (peut être array ou string)
            if (isset($prices['fnac'])) {
                if (is_array($prices['fnac']) && isset($prices['fnac']['formatted_min']) && !empty($prices['fnac']['formatted_min'])) {
                    $prixOccasion[] = "Fnac occasion: " . $prices['fnac']['formatted_min'] . "€ (moyenne: " . $prices['fnac']['formatted_average'] . "€, " . $prices['fnac']['count'] . " offres)";
                } elseif (is_string($prices['fnac']) && $prices['fnac'] !== 'Prix neuf non trouvé') {
                    $prixNeufs[] = "Fnac: " . $prices['fnac'];
                }
            }
            
            // Construire le texte pour l'IA
            $prixText = [];
            if (!empty($prixOccasion)) {
                $prixText[] = "Prix d'occasion trouvés: " . implode(", ", $prixOccasion);
            }
            if (!empty($prixNeufs)) {
                $prixText[] = "Prix neufs trouvés: " . implode(", ", $prixNeufs);
            }
            
            $prixTextFinal = !empty($prixText) ? implode(". ", $prixText) : "Aucun prix trouvé";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert en estimation de prix de mangas d\'occasion. Tu dois répondre UNIQUEMENT avec le prix estimé en euros, sans texte supplémentaire, sans symbole €, juste le nombre avec un point pour les décimales. Par exemple: 12.50 ou 8.00. Base ton estimation sur les prix d\'occasion et neufs fournis, en tenant compte des statistiques de marché (moyennes, nombre d\'offres).'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Estime le prix d'occasion en bon état pour le manga avec l'ISBN {$isbn}. {$prixTextFinal}. Réponds uniquement avec le prix en euros (exemple: 12.50)."
                    ]
                ],
                'max_tokens' => 10,
                'temperature' => 0.3
            ]);

            $price = trim($response->choices[0]->message->content);
            
            // Nettoyer et valider le prix
            $price = preg_replace('/[^0-9.]/', '', $price);
            
            if (is_numeric($price) && $price > 0) {
                return number_format((float)$price, 2) . '€';
            }
            
            return 'Estimation non disponible';
            
        } catch (\Exception $e) {
            return 'Estimation non disponible';
        }
    }

    private function getMangaRarityFromOpenAI($title, $isbn, $prices = [], $popularity = null)
    {
        try {
            // Récupérer les informations complètes du livre
            $bookInfo = $this->isbnService->getBookInfo($isbn);

            // Préparer les données de prix pour l'analyse
            $priceData = [];
            $occasionData = [];
            
            foreach (['amazon', 'cultura', 'fnac'] as $provider) {
                if (isset($prices[$provider])) {
                    $data = $prices[$provider];
                    if (is_array($data)) {
                        // Vérifier si c'est un prix d'occasion (contient des données structurées)
                        if (isset($data['formatted_min']) && !empty($data['formatted_min'])) {
                            $occasionData[] = ucfirst($provider) . " occasion: " . 
                                "Min: " . ($data['formatted_min'] ?? 'N/A') . 
                                ", Max: " . ($data['formatted_max'] ?? 'N/A') . 
                                ", Moyenne: " . ($data['formatted_average'] ?? 'N/A') . 
                                ", Offres: " . ($data['count'] ?? 'N/A');
                        } else {
                            $priceData[] = ucfirst($provider) . " neuf: " . ($data['formatted_min'] ?? 'N/A');
                        }
                    } elseif (is_string($data) && $data !== 'Prix non trouvé') {
                        $priceData[] = ucfirst($provider) . " neuf: " . $data;
                    }
                }
            }
            
            // Construire le texte des prix
            $priceTextParts = [];
            if (!empty($priceData)) {
                $priceTextParts[] = "Prix neufs: " . implode(" | ", $priceData);
            }
            if (!empty($occasionData)) {
                $priceTextParts[] = "Prix d'occasion: " . implode(" | ", $occasionData);
            }
            
            $priceText = !empty($priceTextParts) ? implode(". ", $priceTextParts) : "Aucun prix disponible";
            
            // Préparer les informations du livre
            $bookInfoText = "";
            if ($bookInfo) {
                $bookInfoText = "Informations du livre: " .
                    "Titre: " . ($bookInfo['title'] ?? $title) . ", " .
                    "Auteur: " . ($bookInfo['author'] ?? 'Inconnu') . ", " .
                    "Éditeur: " . ($bookInfo['publisher'] ?? 'Inconnu') . ", " .
                    "Date de publication: " . ($bookInfo['published_date'] ?? 'Inconnue') . ". ";
            }

            // Préparer les données de popularité AniList
            $popularityText = "";
            if ($popularity && $popularity['success']) {
                $popularityText = "Données AniList: " .
                    "Score de popularité: " . ($popularity['popularity_score'] ?? 'N/A') . "/100, " .
                    "Note moyenne: " . ($popularity['rating'] ?? 'N/A') . "/100, " .
                    "Niveau de popularité: " . ($popularity['popularity_level'] ?? 'N/A') . ", " .
                    "Statut: " . ($popularity['status'] ?? 'N/A') . ". ";
            }

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert en évaluation de rareté et de valeur de mangas. Analyse les informations du livre, les prix fournis ET les données de popularité AniList pour donner un score de rareté de 1 à 10 (1 = très commun, 10 = très rare) et estimer la valeur du manga selon trois états: correct, bon, excellent. CRITIQUE: L\'estimation de rareté doit se baser à 90% sur les prix d\'occasion disponibles. Si des prix d\'occasion élevés sont disponibles avec peu d\'offres, cela indique une forte rareté. Si les prix d\'occasion sont bas avec beaucoup d\'offres, cela indique une faible rareté. Les autres facteurs (ancienneté, éditeur, auteur, popularité AniList) ne représentent que 10% de l\'évaluation. Pour l\'estimation de valeur, base-toi EXCLUSIVEMENT sur les prix d\'occasion disponibles. IMPORTANT: Tes estimations de valeur doivent être RÉALISTES par rapport aux prix d\'occasion trouvés. Si le prix minimum d\'occasion est 1.61€ et le maximum 12.70€, tes estimations ne doivent pas dépasser cette fourchette. Utilise les prix d\'occasion réels comme référence absolue. Si l\'auteur est inconnu ou non spécifié, ne pas en tenir compte dans l\'évaluation et ne pas le mentionner dans l\'explication. Réponds au format JSON: {"score": X, "explanation": "texte", "factors": ["facteur1", "facteur2", "facteur3"], "value_estimation": {"correct": float, "bon": float, "excellent": float}} où les valeurs dans value_estimation sont des nombres décimaux (exemple: 12.50, 15.75, 20.00).'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyse la rareté du manga '{$title}' (ISBN: {$isbn}). {$bookInfoText}{$popularityText}Prix du marché: {$priceText}. Donne un score de rareté, une explication détaillée et liste les facteurs principaux qui influencent cette rareté."
                    ]
                ],
                'max_tokens' => 400,
                'temperature' => 0.3
            ]);

            $content = trim($response->choices[0]->message->content);
            
            // Essayer de parser le JSON
            $decoded = json_decode($content, true);
            if ($decoded && isset($decoded['score']) && isset($decoded['explanation'])) {
                // Traiter les valeurs d'estimation pour les convertir en float et ajouter le symbole euros
                $valueEstimation = $decoded['value_estimation'] ?? [
                    'correct' => 0.0,
                    'bon' => 0.0,
                    'excellent' => 0.0
                ];
                
                // Convertir en float et formater avec euros
                foreach (['correct', 'bon', 'excellent'] as $condition) {
                    if (isset($valueEstimation[$condition])) {
                        $value = (float)$valueEstimation[$condition];
                        $valueEstimation[$condition] = $value > 0 ? number_format($value, 2) . '€' : 'Non disponible';
                    }
                }
                
                return [
                    'score' => (int)$decoded['score'],
                    'explanation' => $decoded['explanation'],
                    'factors' => $decoded['factors'] ?? [],
                    'value_estimation' => $valueEstimation
                ];
            }
            
            // Fallback si le JSON n'est pas valide
            return [
                'score' => 5,
                'explanation' => 'Analyse de rareté non disponible',
                'factors' => [],
                'value_estimation' => [
                    'correct' => 0.0,
                    'bon' => 0.0,
                    'excellent' => 0.0
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'score' => 5,
                'explanation' => 'Erreur lors de l\'analyse de rareté',
                'factors' => [],
                'value_estimation' => [
                    'correct' => 0.0,
                    'bon' => 0.0,
                    'excellent' => 0.0
                ]
            ];
        }
    }

    public function verifyIsbn(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|max:20',
        ]);

        $isbn = $request->input('isbn');
        
        // Nettoyer l'ISBN (enlever les espaces et tirets)
        $isbn = preg_replace('/[^0-9X]/', '', strtoupper($isbn));
        
        // Vérifier la validité de base de l'ISBN
        if (!$this->isbnService->isValidIsbn($isbn)) {
            return response()->json([
                'valid' => false,
                'message' => 'Format ISBN invalide'
            ]);
        }

        // Récupérer les informations du livre
        $bookInfo = $this->isbnService->getBookInfo($isbn);
        
        if (!$bookInfo) {
            return response()->json([
                'valid' => false,
                'message' => 'ISBN non trouvé dans les bases de données'
            ]);
        }

        return response()->json([
            'valid' => true,
            'isbn' => $isbn,
            'title' => $bookInfo['title'],
            'author' => $bookInfo['author'] ?? 'Auteur inconnu',
            'publisher' => $bookInfo['publisher'] ?? 'Éditeur inconnu',
            'published_date' => $bookInfo['published_date'] ?? 'Date inconnue',
            'message' => 'Livre trouvé : ' . $bookInfo['title']
        ]);
    }

    public function searchPricesOnly($isbn)
    {
        $title = $this->isbnService->getTitleFromIsbn($isbn);
        $searchData = $this->searchPrices($isbn, $title, null, null);
        
        // Vérifier que prices est bien un tableau
        if (!is_array($searchData['prices'])) {
            return [
                'prices' => [],
                'occasion_price' => null,
                'title' => $title
            ];
        }
        
        // Convertir les prix en float
        $prices = [];
        foreach ($searchData['prices'] as $site => $price) {
            if ($price && $price !== "Prix non trouvé") {
                $prices[$site] = $this->isbnService->extractPriceFromString($price);
            } else {
                $prices[$site] = null;
            }
        }
        
        // Convertir le prix d'occasion en float
        $occasionPrice = null;
        if ($searchData['occasion_price']) {
            $occasionPrice = $this->isbnService->extractPriceFromString($searchData['occasion_price']);
        }
        
        return [
            'prices' => $prices,
            'occasion_price' => $occasionPrice,
            'title' => $title
        ];
    }
} 