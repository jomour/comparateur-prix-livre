<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\PriceParserService;
use App\Models\HistoriqueSearch;
use OpenAI\Laravel\Facades\OpenAI;

class PriceController extends Controller
{
    protected $priceParser;

    public function __construct(PriceParserService $priceParser)
    {
        $this->priceParser = $priceParser;
    }

    public function index()
    {
        return view('price.search');
    }

    public function historique()
    {
        $searches = HistoriqueSearch::with('user')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('price.historique', compact('searches'));
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

        $apiKey = env('SCRAPER_API_KEY');

        // Récupérer le titre via l'API OpenLibrary
        $title = $this->getTitleFromIsbn($isbn);

        $results = [];
        $prices = [];

        // Créer le dossier storage/app/public/results s'il n'existe pas
        $resultsPath = storage_path('app/public/results');
        if (!file_exists($resultsPath)) {
            mkdir($resultsPath, 0755, true);
        }

        // AMAZON
        $target_url = "https://www.amazon.fr/dp/" . $isbn;
        $encoded_url = urlencode($target_url);
        $api_url = "https://api.scraperapi.com/?api_key={$apiKey}&url=" . $encoded_url . "&country_code=fr";
        
        try {
            $response = Http::timeout(60)->get($api_url);
            $results['amazon'] = $response->body();
            $amazonFile = $resultsPath . '/index_amazon_' . $search->id . '.html';
            file_put_contents($amazonFile, $response->body());
            chmod($amazonFile, 0644);
        } catch (\Exception $e) {
            $results['amazon'] = '<html><body>Erreur lors de la récupération Amazon</body></html>';
            $amazonFile = $resultsPath . '/index_amazon_' . $search->id . '.html';
            file_put_contents($amazonFile, $results['amazon']);
            chmod($amazonFile, 0644);
        }
        
        // Parser le prix Amazon
        $prices['amazon'] = $this->priceParser->parseAmazonPrice($results['amazon']);

        // CULTURA
        $target_url = "https://www.cultura.com/search/results?search_query=" . $isbn;
        $encoded_url = urlencode($target_url);
        $api_url = "https://api.scraperapi.com/?api_key={$apiKey}&url=" . $encoded_url . "&country_code=fr";
        
        try {
            $response = Http::timeout(60)->get($api_url);
            $results['cultura'] = $response->body();
            $culturaFile = $resultsPath . '/index_cultura_' . $search->id . '.html';
            file_put_contents($culturaFile, $response->body());
            chmod($culturaFile, 0644);
        } catch (\Exception $e) {
            $results['cultura'] = '<html><body>Erreur lors de la récupération Cultura</body></html>';
            $culturaFile = $resultsPath . '/index_cultura_' . $search->id . '.html';
            file_put_contents($culturaFile, $results['cultura']);
            chmod($culturaFile, 0644);
        }
        
        // Parser le prix Cultura
        $prices['cultura'] = $this->priceParser->parseCulturaPrice($results['cultura']);

        // FNAC
        $target_url = "https://www.fnac.com/SearchResult/ResultList.aspx?Search=" . urlencode($title);
        $encoded_url = urlencode($target_url);
        $api_url = "https://api.scraperapi.com/?api_key={$apiKey}&url=" . $encoded_url . "&country_code=fr";
        
        try {
            $response = Http::timeout(60)->get($api_url);
            $results['fnac'] = $response->body();
            $fnacFile = $resultsPath . '/index_fnac_' . $search->id . '.html';
            file_put_contents($fnacFile, $response->body());
            chmod($fnacFile, 0644);
        } catch (\Exception $e) {
            $results['fnac'] = '<html><body>Erreur lors de la récupération Fnac</body></html>';
            $fnacFile = $resultsPath . '/index_fnac_' . $search->id . '.html';
            file_put_contents($fnacFile, $results['fnac']);
            chmod($fnacFile, 0644);
        }
        
        // Parser le prix Fnac
        $prices['fnac'] = $this->priceParser->parseFnacPrice($results['fnac']);

        // Obtenir l'estimation de prix d'occasion via OpenAI avec les prix neufs
        $occasionPrice = $this->getOccasionPriceEstimation($isbn, $prices);

        // Mettre à jour l'historique avec les prix uniquement
        $search->update([
            'prix_fnac' => $prices['fnac'],
            'prix_amazon' => $prices['amazon'],
            'prix_cultura' => $prices['cultura'],
            'estimation_occasion' => $occasionPrice,
        ]);

        return view('price.results', [
            'isbn' => $isbn,
            'title' => $title,
            'results' => $results,
            'prices' => $prices,
            'historique_id' => $search->id,
            'occasion_price' => $occasionPrice
        ]);
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
            if (!$search || $search->user_id !== auth()->id()) {
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
            if (!$search || $search->user_id !== auth()->id()) {
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
            if (!$search || $search->user_id !== auth()->id()) {
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
} 