<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\PriceParserService;
use App\Services\SeoService;
use App\Models\HistoriqueSearch;
use OpenAI\Laravel\Facades\OpenAI;

class PriceController extends Controller
{
    protected $priceParser;

    public function __construct(PriceParserService $priceParser)
    {
        $this->priceParser = $priceParser;
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
        $title = $this->getTitleFromIsbn($isbn);

        // Créer le dossier storage/app/public/results s'il n'existe pas
        $resultsPath = storage_path('app/public/results');
        if (!file_exists($resultsPath)) {
            mkdir($resultsPath, 0755, true);
        }

        // Récupérer les prix et résultats
        $searchId = $search->getKey();
        $searchData = $this->searchPrices($isbn, $title, $searchId, $resultsPath);

        // Mettre à jour l'historique avec les prix uniquement
        $search->update([
            'prix_fnac' => $searchData['prices']['fnac'],
            'prix_amazon' => $searchData['prices']['amazon'],
            'prix_cultura' => $searchData['prices']['cultura'],
            'estimation_occasion' => $searchData['occasion_price'],
        ]);

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
        $apiKey = env('SCRAPER_API_KEY');
        $results = [];
        $prices = [];

        // AMAZON
        $target_url = "https://www.amazon.fr/dp/" . $isbn;
        $encoded_url = urlencode($target_url);
        $api_url = "https://api.scraperapi.com/?api_key={$apiKey}&url=" . $encoded_url . "&country_code=fr";
        
        try {
            $response = Http::timeout(60)->get($api_url);
            $results['amazon'] = $response->body();
            $prices['amazon'] = $this->priceParser->parseAmazonPrice($results['amazon']);
            
            // Sauvegarder le fichier si un chemin est fourni
            if ($resultsPath && $searchId) {
                $amazonFile = $resultsPath . '/index_amazon_' . $searchId . '.html';
                file_put_contents($amazonFile, $response->body());
                chmod($amazonFile, 0644);
            }
        } catch (\Exception $e) {
            $results['amazon'] = '<html><body>Erreur lors de la récupération Amazon</body></html>';
            $prices['amazon'] = null;
            
            if ($resultsPath && $searchId) {
                $amazonFile = $resultsPath . '/index_amazon_' . $searchId . '.html';
                file_put_contents($amazonFile, $results['amazon']);
                chmod($amazonFile, 0644);
            }
        }

        // CULTURA
        $target_url = "https://www.cultura.com/search/results?search_query=" . $isbn;
        $encoded_url = urlencode($target_url);
        $api_url = "https://api.scraperapi.com/?api_key={$apiKey}&url=" . $encoded_url . "&country_code=fr";
        
        try {
            $response = Http::timeout(60)->get($api_url);
            $results['cultura'] = $response->body();
            $prices['cultura'] = $this->priceParser->parseCulturaPrice($results['cultura']);
            
            if ($resultsPath && $searchId) {
                $culturaFile = $resultsPath . '/index_cultura_' . $searchId . '.html';
                file_put_contents($culturaFile, $response->body());
                chmod($culturaFile, 0644);
            }
        } catch (\Exception $e) {
            $results['cultura'] = '<html><body>Erreur lors de la récupération Cultura</body></html>';
            $prices['cultura'] = null;
            
            if ($resultsPath && $searchId) {
                $culturaFile = $resultsPath . '/index_cultura_' . $searchId . '.html';
                file_put_contents($culturaFile, $results['cultura']);
                chmod($culturaFile, 0644);
            }
        }

        // FNAC
        $target_url = "https://www.fnac.com/SearchResult/ResultList.aspx?Search=" . urlencode($title);
        $encoded_url = urlencode($target_url);
        $api_url = "https://api.scraperapi.com/?api_key={$apiKey}&url=" . $encoded_url . "&country_code=fr";
        
        try {
            $response = Http::timeout(60)->get($api_url);
            $results['fnac'] = $response->body();
            $prices['fnac'] = $this->priceParser->parseFnacPrice($results['fnac']);
            
            if ($resultsPath && $searchId) {
                $fnacFile = $resultsPath . '/index_fnac_' . $searchId . '.html';
                file_put_contents($fnacFile, $response->body());
                chmod($fnacFile, 0644);
            }
        } catch (\Exception $e) {
            $results['fnac'] = '<html><body>Erreur lors de la récupération Fnac</body></html>';
            $prices['fnac'] = null;
            
            if ($resultsPath && $searchId) {
                $fnacFile = $resultsPath . '/index_fnac_' . $searchId . '.html';
                file_put_contents($fnacFile, $results['fnac']);
                chmod($fnacFile, 0644);
            }
        }

        // Obtenir l'estimation de prix d'occasion
        $occasionPrice = $this->getOccasionPriceEstimation($isbn, $prices);

        return [
            'results' => $results,
            'prices' => $prices,
            'occasion_price' => $occasionPrice
        ];
    }

    private function getTitleFromIsbn($isbn)
    {
        try {
            // API OpenLibrary
            $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";
            $response = Http::get($url);
            $data = $response->json();

            if (isset($data["ISBN:{$isbn}"]) && isset($data["ISBN:{$isbn}"]['title'])) {
                return $data["ISBN:{$isbn}"]['title'];
            }

            // Fallback: essayer Google Books API
            $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}";
            $response = Http::get($url);
            $data = $response->json();

            if (isset($data['items'][0]['volumeInfo']['title'])) {
                return $data['items'][0]['volumeInfo']['title'];
            }

            // Si aucune API ne fonctionne, retourner un titre par défaut
            return "Livre ISBN: {$isbn}";

        } catch (\Exception $e) {
            // En cas d'erreur, retourner un titre par défaut
            return "Livre ISBN: {$isbn}";
        }
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
            // Préparer les prix neufs pour le prompt
            $prixNeufs = [];
            if (isset($prices['amazon']) && $prices['amazon'] !== 'Prix non trouvé') {
                $prixNeufs[] = "Amazon: " . $prices['amazon'];
            }
            if (isset($prices['cultura']) && $prices['cultura'] !== 'Prix neuf non trouvé') {
                $prixNeufs[] = "Cultura: " . $prices['cultura'];
            }
            if (isset($prices['fnac']) && $prices['fnac'] !== 'Prix neuf non trouvé') {
                $prixNeufs[] = "Fnac: " . $prices['fnac'];
            }
            
            $prixNeufsText = !empty($prixNeufs) ? "Prix neufs trouvés: " . implode(", ", $prixNeufs) : "Aucun prix neuf trouvé";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert en estimation de prix de mangas d\'occasion. Tu dois répondre UNIQUEMENT avec le prix estimé en euros, sans texte supplémentaire, sans symbole €, juste le nombre avec un point pour les décimales. Par exemple: 12.50 ou 8.00. Base ton estimation sur les prix neufs fournis et les tendances du marché des mangas d\'occasion.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Estime le prix d'occasion en bon état pour le manga avec l'ISBN {$isbn}. {$prixNeufsText}. Réponds uniquement avec le prix en euros (exemple: 12.50)."
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
        if (!$this->isValidIsbn($isbn)) {
            return response()->json([
                'valid' => false,
                'message' => 'Format ISBN invalide'
            ]);
        }

        // Récupérer les informations du livre
        $bookInfo = $this->getBookInfo($isbn);
        
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

    private function isValidIsbn($isbn)
    {
        $length = strlen($isbn);
        
        // ISBN-10 ou ISBN-13
        if ($length !== 10 && $length !== 13) {
            return false;
        }

        // Vérification de la checksum pour ISBN-10
        if ($length === 10) {
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += (10 - $i) * intval($isbn[$i]);
            }
            $checkDigit = $isbn[9] === 'X' ? 10 : intval($isbn[9]);
            return ($sum + $checkDigit) % 11 === 0;
        }

        // Vérification de la checksum pour ISBN-13
        if ($length === 13) {
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += intval($isbn[$i]) * ($i % 2 === 0 ? 1 : 3);
            }
            $checkDigit = intval($isbn[12]);
            return (10 - ($sum % 10)) % 10 === $checkDigit;
        }

        return false;
    }

    private function getBookInfo($isbn)
    {
        try {
            // Essayer OpenLibrary d'abord
            $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";
            $response = Http::timeout(10)->get($url);
            $data = $response->json();

            if (isset($data["ISBN:{$isbn}"])) {
                $book = $data["ISBN:{$isbn}"];
                return [
                    'title' => $book['title'] ?? 'Titre inconnu',
                    'author' => isset($book['authors'][0]['name']) ? $book['authors'][0]['name'] : null,
                    'publisher' => isset($book['publishers'][0]['name']) ? $book['publishers'][0]['name'] : null,
                    'published_date' => $book['publish_date'] ?? null
                ];
            }

            // Fallback: Google Books API
            $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}";
            $response = Http::timeout(10)->get($url);
            $data = $response->json();

            if (isset($data['items'][0]['volumeInfo'])) {
                $book = $data['items'][0]['volumeInfo'];
                return [
                    'title' => $book['title'] ?? 'Titre inconnu',
                    'author' => isset($book['authors'][0]) ? $book['authors'][0] : null,
                    'publisher' => $book['publisher'] ?? null,
                    'published_date' => $book['publishedDate'] ?? null
                ];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    public function searchPricesOnly($isbn)
    {
        $title = $this->getTitleFromIsbn($isbn);
        $searchData = $this->searchPrices($isbn, $title, null, null);
        
        // Convertir les prix en float
        $prices = [];
        foreach ($searchData['prices'] as $site => $price) {
            if ($price && $price !== "Prix non trouvé") {
                $prices[$site] = $this->extractPriceFromString($price);
            } else {
                $prices[$site] = null;
            }
        }
        
        // Convertir le prix d'occasion en float
        $occasionPrice = null;
        if ($searchData['occasion_price']) {
            $occasionPrice = $this->extractPriceFromString($searchData['occasion_price']);
        }
        
        return [
            'prices' => $prices,
            'occasion_price' => $occasionPrice,
            'title' => $title
        ];
    }

    private function extractPriceFromString($priceString)
    {
        if (!$priceString || $priceString === "Prix non trouvé") {
            return null;
        }
        
        // Nettoyer la chaîne
        $price = trim($priceString);
        
        // Supprimer le symbole euro et autres caractères
        $price = str_replace(['€', 'EUR', 'euros', 'euro'], '', $price);
        $price = preg_replace('/[^\d.,]/', '', $price);
        
        // Gérer les virgules et points
        if (strpos($price, ',') !== false && strpos($price, '.') !== false) {
            // Format "1,234.56" ou "1.234,56"
            if (strpos($price, ',') < strpos($price, '.')) {
                // "1,234.56" -> virgule pour milliers
                $price = str_replace(',', '', $price);
            } else {
                // "1.234,56" -> point pour milliers, virgule pour décimales
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '.', $price);
            }
        } else {
            // Format simple avec virgule ou point
            $price = str_replace(',', '.', $price);
        }
        
        $numericPrice = floatval($price);
        
        // Vérifier que c'est un prix valide
        if ($numericPrice > 0 && $numericPrice < 10000) {
            return $numericPrice;
        }
        
        return null;
    }
} 