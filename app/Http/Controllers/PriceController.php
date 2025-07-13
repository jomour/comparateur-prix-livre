<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IsbnService;
use App\Services\SeoService;
use App\Services\AnilistService;
use App\Actions\EstimateMangaPrice;
use OpenAI\Laravel\Facades\OpenAI;


class PriceController extends Controller
{
    protected \App\Services\AmazonPriceParserService $amazonPriceParser;
    protected \App\Services\CulturaPriceParserService $culturaPriceParser;
    protected \App\Services\FnacPriceParserService $fnacPriceParser;
    protected $isbnService;
    protected $anilistService;
    protected $estimateMangaPriceAction;

    public function __construct(
        IsbnService $isbnService,
        AnilistService $anilistService,
        EstimateMangaPrice $estimateMangaPriceAction
    ) {
        $this->amazonPriceParser = app(\App\Services\AmazonPriceParserService::class);
        $this->culturaPriceParser = app(\App\Services\CulturaPriceParserService::class);
        $this->fnacPriceParser = app(\App\Services\FnacPriceParserService::class);
        $this->isbnService = $isbnService;
        $this->anilistService = $anilistService;
        $this->estimateMangaPriceAction = $estimateMangaPriceAction;
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
        
        // Utiliser l'Action EstimateMangaPrice
        $result = $this->estimateMangaPriceAction->execute($isbn);

        $search = $result['search'];
        $searchData = $result['searchData'];
        $popularity = $result['popularity'];
        $rarity = $result['rarity'];

        // Métadonnées SEO pour les résultats
        $meta = SeoService::getResultsMeta($isbn, $search->title, $searchData['prices']);
        $seoType = 'product';
        $structuredData = SeoService::getStructuredData('product', [
            'title' => $search->title,
            'isbn' => $isbn,
            'prices' => $searchData['prices']
        ]);
        
        // Si c'est une requête AJAX, retourner JSON
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'redirect' => \App\Helpers\LocalizedRoute::url('price.results', ['isbn' => $isbn])
            ]);
        }
        
        return view('price.results', [
            'isbn' => $isbn,
            'title' => $search->title,
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

    public function showResults(Request $request)
    {
        $isbn = $request->query('isbn');
        
        if (!$isbn) {
            return redirect()->route('fr.comparateur.prix');
        }
        
        // Utiliser l'Action EstimateMangaPrice
        $result = $this->estimateMangaPriceAction->execute($isbn);

        $search = $result['search'];
        $searchData = $result['searchData'];
        $popularity = $result['popularity'];
        $rarity = $result['rarity'];

        // Métadonnées SEO pour les résultats
        $meta = SeoService::getResultsMeta($isbn, $search->title, $searchData['prices']);
        $seoType = 'product';
        $structuredData = SeoService::getStructuredData('product', [
            'title' => $search->title,
            'isbn' => $isbn,
            'prices' => $searchData['prices']
        ]);
        
        return view('price.results', [
            'isbn' => $isbn,
            'title' => $search->title,
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


} 