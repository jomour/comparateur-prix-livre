<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IsbnService;
use App\Services\SeoService;
use App\Models\HistoriqueSearch;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\PriceParserInterface;
use App\ValueObjects\PriceStats;

class PriceController extends Controller
{
    protected $amazonPriceParser;
    protected $culturaPriceParser;
    protected $fnacPriceParser;
    protected $isbnService;

    public function __construct(
        IsbnService $isbnService
    ) {
        $this->amazonPriceParser = app(PriceParserInterface::class . '.amazon');
        $this->culturaPriceParser = app(PriceParserInterface::class . '.cultura');
        $this->fnacPriceParser = app(PriceParserInterface::class . '.fnac');
        $this->isbnService = $isbnService;
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

    public function historique()
    {
        $searches = HistoriqueSearch::with('user')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Métadonnées SEO
        $meta = SeoService::getHistoryMeta();
        $seoType = 'website';

        return view('price.historique', compact('searches', 'meta', 'seoType'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|max:20',
        ]);

        $isbn = $request->input('isbn');
        
        // Créer l'enregistrement de recherche
        $search = new HistoriqueSearch();
        $search->user_id = auth()->id();
        $search->isbn = $isbn;
        $search->save();

        // Récupérer le titre via l'API OpenLibrary
        $title = $this->isbnService->getTitleFromIsbn($isbn);

        // Créer le dossier storage/app/public/results s'il n'existe pas
        $resultsPath = storage_path('app/public/results');
        if (!file_exists($resultsPath)) {
            mkdir($resultsPath, 0755, true);
        }

        // Récupérer les prix et résultats
        $searchId = $search->getKey();
        $searchData = $this->searchPrices($isbn, $title, $searchId, $resultsPath);

        // Métadonnées SEO pour les résultats
        $meta = SeoService::getResultsMeta($isbn, $title, $searchData['prices']);
        $seoType = 'product';
        $structuredData = SeoService::getStructuredData('product', [
            'title' => $title,
            'isbn' => $isbn,
            'prices' => $searchData['prices']
        ]);

        return view('price.results', [
            'isbn' => $isbn,
            'title' => $title,
            'results' => $searchData['results'],
            'prices' => $searchData['prices'],
            'historique_id' => $search->getKey(),
            'occasion_price' => $searchData['occasion_price'],
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
    



    public function showAmazon($id = null)
    {
        if ($id) {
            // Vérifier que l'utilisateur a accès à cette recherche
            $search = HistoriqueSearch::find($id);
            if (!$search || $search->getAttribute('user_id') !== auth()->id()) {
                abort(403, 'Accès non autorisé');
            }
            
            // Afficher le fichier HTML d'une recherche spécifique
            $filePath = storage_path("app/public/results/index_amazon_{$id}.html");
            
            if (!file_exists($filePath)) {
                abort(404, 'Fichier Amazon non trouvé');
            }
            
            $content = file_get_contents($filePath);
            
            return response($content)
                ->header('Content-Type', 'text/html')
                ->header('Content-Length', strlen($content));
        }

        // Fallback vers le fichier le plus récent
        $filePath = storage_path('app/public/results/index_amazon.html');
        
        if (!file_exists($filePath)) {
            abort(404, 'Fichier Amazon non trouvé');
        }
        
        $content = file_get_contents($filePath);
        
        return response($content)
            ->header('Content-Type', 'text/html')
            ->header('Content-Length', strlen($content));
    }

    public function showCultura($id = null)
    {
        if ($id) {
            // Vérifier que l'utilisateur a accès à cette recherche
            $search = HistoriqueSearch::find($id);
            if (!$search || $search->getAttribute('user_id') !== auth()->id()) {
                abort(403, 'Accès non autorisé');
            }
            
            // Afficher le fichier HTML d'une recherche spécifique
            $filePath = storage_path("app/public/results/index_cultura_{$id}.html");
            
            if (!file_exists($filePath)) {
                abort(404, 'Fichier Cultura non trouvé');
            }
            
            $content = file_get_contents($filePath);
            
            return response($content)
                ->header('Content-Type', 'text/html')
                ->header('Content-Length', strlen($content));
        }

        // Fallback vers le fichier le plus récent
        $filePath = storage_path('app/public/results/index_cultura.html');
        
        if (!file_exists($filePath)) {
            abort(404, 'Fichier Cultura non trouvé');
        }
        
        $content = file_get_contents($filePath);
        
        return response($content)
            ->header('Content-Type', 'text/html')
            ->header('Content-Length', strlen($content));
    }

    public function showFnac($id = null)
    {
        if ($id) {
            // Vérifier que l'utilisateur a accès à cette recherche
            $search = HistoriqueSearch::find($id);
            if (!$search || $search->getAttribute('user_id') !== auth()->id()) {
                abort(403, 'Accès non autorisé');
            }
            
            // Afficher le fichier HTML d'une recherche spécifique
            $filePath = storage_path("app/public/results/index_fnac_{$id}.html");
            
            if (!file_exists($filePath)) {
                abort(404, 'Fichier Fnac non trouvé');
            }
            
            $content = file_get_contents($filePath);
            
            return response($content)
                ->header('Content-Type', 'text/html')
                ->header('Content-Length', strlen($content));
        }

        // Fallback vers le fichier le plus récent
        $filePath = storage_path('app/public/results/index_fnac.html');
        
        if (!file_exists($filePath)) {
            abort(404, 'Fichier Fnac non trouvé');
        }
        
        $content = file_get_contents($filePath);
        
        return response($content)
            ->header('Content-Type', 'text/html')
            ->header('Content-Length', strlen($content));
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