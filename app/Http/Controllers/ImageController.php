<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PriceController;

class ImageController extends Controller
{
    protected $priceController;

    public function __construct(PriceController $priceController)
    {
        $this->priceController = $priceController;
    }

    public function index()
    {
        return view('image.upload');
    }

    public function upload(Request $request)
    {
        // Validation renforcée
        $request->validate([
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048', // 2MB max
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ]
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Vérifications de sécurité supplémentaires
            if (!$this->isSecureImage($image)) {
                return back()->with('error', 'Fichier non sécurisé détecté.');
            }
            
            // Nettoyer et sécuriser le nom de fichier
            $originalName = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $imageName = $this->generateSecureFileName($originalName, $extension);
            
            // Stockage sécurisé
            $destinationPath = storage_path('app/public/images');
            
            // S'assurer que le dossier existe avec les bonnes permissions
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            // Vérifier que le dossier est sécurisé
            if (!is_writable($destinationPath)) {
                return back()->with('error', 'Erreur de permissions sur le dossier de stockage.');
            }
            
            try {
                // Déplacer le fichier avec vérification
                $fullPath = $destinationPath . '/' . $imageName;
                $image->move($destinationPath, $imageName);
                
                // Vérification post-upload
                if (!file_exists($fullPath) || !$this->isValidImageFile($fullPath)) {
                    // Nettoyer le fichier invalide
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                    return back()->with('error', 'Fichier image invalide détecté.');
                }
                
                // Analyser l'image avec OpenAI
                $mangas = $this->analyzeImageWithOpenAI($fullPath);
                
                // Nettoyer l'image après analyse (optionnel, pour économiser l'espace)
                // unlink($fullPath);
                
                return back()->with('success', 'Image analysée avec succès!')
                            ->with('image', $imageName)
                            ->with('mangas', $mangas);
                            
            } catch (\Exception $e) {
                // Nettoyer en cas d'erreur
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                Log::error('Erreur lors du traitement de l\'image : ' . $e->getMessage());
                return back()->with('error', 'Erreur lors du traitement de l\'image.');
            }
        }

        return back()->with('error', 'Erreur lors du chargement de l\'image.');
    }

    private function isSecureImage($image)
    {
        // Vérifier le type MIME réel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $image->getPathname());
        finfo_close($finfo);
        
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($mimeType, $allowedMimes)) {
            Log::warning('Type MIME non autorisé détecté : ' . $mimeType);
            return false;
        }
        
        // Vérifier la taille du fichier
        if ($image->getSize() > 2 * 1024 * 1024) { // 2MB
            Log::warning('Fichier trop volumineux : ' . $image->getSize() . ' bytes');
            return false;
        }
        
        // Vérifier les dimensions
        $imageInfo = getimagesize($image->getPathname());
        if (!$imageInfo) {
            Log::warning('Impossible de lire les dimensions de l\'image');
            return false;
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        if ($width < 100 || $height < 100 || $width > 4000 || $height > 4000) {
            Log::warning('Dimensions d\'image non autorisées : ' . $width . 'x' . $height);
            return false;
        }
        
        return true;
    }

    private function generateSecureFileName($originalName, $extension)
    {
        // Nettoyer le nom original
        $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
        $cleanName = substr($cleanName, 0, 50); // Limiter la longueur
        
        // Générer un hash unique
        $hash = hash('sha256', uniqid() . $originalName . time());
        
        // Combiner pour un nom sécurisé
        return $hash . '_' . $cleanName . '.' . $extension;
    }

    private function isValidImageFile($filePath)
    {
        // Vérifier que c'est bien une image valide
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            return false;
        }
        
        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        return in_array($mimeType, $allowedMimes);
    }

    private function analyzeImageWithOpenAI($imagePath)
    {
        try {
            // Encoder l'image en base64
            $imageData = base64_encode(file_get_contents($imagePath));
            
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert en mangas et en reconnaissance d\'images. Analyse cette image de lot de mangas et liste TOUS les mangas visibles. Sois très attentif et ne manque aucun manga, même s\'il est partiellement visible ou dans un coin. Pour chaque manga, extrait le titre complet. Retourne uniquement un JSON valide avec cette structure : [{"title": "Titre complet du manga"}]. Ne retourne que le JSON, pas d\'explication. Assure-toi de bien voir tous les mangas dans l\'image.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyse cette image de lot de mangas et liste TOUS les mangas visibles avec leur titre complet. Sois très attentif et ne manque aucun manga. Retourne uniquement un JSON valide.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:image/jpeg;base64,' . $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 2000
            ]);

            $rawResponse = trim($response->choices[0]->message->content);
            Log::info("Réponse brute de l'IA : " . $rawResponse);

            // Nettoyer la réponse pour extraire le JSON
            $jsonStart = strpos($rawResponse, '[');
            $jsonEnd = strrpos($rawResponse, ']');
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonString = substr($rawResponse, $jsonStart, $jsonEnd - $jsonStart + 1);
            } else {
                $jsonString = $rawResponse;
            }

            $mangas = json_decode($jsonString, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("Erreur JSON : " . json_last_error_msg());
                return [];
            }

            if (!is_array($mangas)) {
                Log::error("La réponse n'est pas un tableau : " . gettype($mangas));
                return [];
            }

            // Si aucun manga n'est détecté, retourner un tableau vide
            if (empty($mangas)) {
                Log::info("Aucun manga détecté dans l'image");
                return [];
            }

            // Rechercher les ISBN pour chaque manga via les API
            $mangasWithIsbn = [];
            foreach ($mangas as $manga) {
                if (isset($manga['title']) && !empty($manga['title'])) {
                    $isbn = $this->findIsbnByTitle($manga['title']);
                    $mangasWithIsbn[] = [
                        'title' => $manga['title'],
                        'isbn' => $isbn,
                        'isDuplicate' => false
                    ];
                }
            }

            // Détecter les doublons d'ISBN
            $duplicateIsbns = $this->detectDuplicateIsbns($mangasWithIsbn);
            foreach ($mangasWithIsbn as &$manga) {
                if (in_array($manga['isbn'], $duplicateIsbns)) {
                    $manga['isDuplicate'] = true;
                }
            }

            return $mangasWithIsbn;

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'analyse OpenAI : " . $e->getMessage());
            return [];
        }
    }

    private function isValidIsbn10($isbn)
    {
        if (strlen($isbn) !== 10) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (10 - $i) * intval($isbn[$i]);
        }
        $checkDigit = $isbn[9] === 'X' ? 10 : intval($isbn[9]);
        return ($sum + $checkDigit) % 11 === 0;
    }

    private function detectDuplicateIsbns($mangas)
    {
        $isbnCounts = [];
        $duplicateIsbns = [];
        
        // Compter les occurrences de chaque ISBN
        foreach ($mangas as $manga) {
            $isbn = $manga['isbn'];
            if ($isbn && $isbn !== 'Non trouvé' && $isbn !== 'Erreur de recherche') {
                if (!isset($isbnCounts[$isbn])) {
                    $isbnCounts[$isbn] = [];
                }
                $isbnCounts[$isbn][] = $manga['title'];
            }
        }
        
        // Identifier les ISBN qui apparaissent plus d'une fois
        foreach ($isbnCounts as $isbn => $titles) {
            if (count($titles) > 1) {
                $duplicateIsbns[] = $isbn;
            }
        }
        
        return $duplicateIsbns;
    }

    private function findIsbnByTitle($title)
    {
        try {
            // Nettoyer le titre pour la recherche
            $cleanData = $this->cleanTitleForSearch($title);
            $cleanTitle = $cleanData['title'];
            $tomeNumber = $cleanData['tome'];
            
            // Essayer plusieurs APIs pour trouver l'ISBN
            
            // 1. Google Books API (le plus fiable pour les mangas)
            $isbn = $this->searchGoogleBooks($cleanTitle, $tomeNumber);
            if ($isbn) {
                return $isbn;
            }
            
            // 2. Essayer avec des variations du titre
            $variations = $this->generateTitleVariations($cleanTitle, $tomeNumber);
            foreach ($variations as $variation) {
                $isbn = $this->searchGoogleBooks($variation['title'], $variation['tome']);
                if ($isbn) {
                    return $isbn;
                }
            }
            
            // 3. OpenLibrary API
            $isbn = $this->searchOpenLibrary($cleanTitle, $tomeNumber);
            if ($isbn) {
                return $isbn;
            }
            
            // 4. Essayer les variations avec OpenLibrary
            foreach ($variations as $variation) {
                $isbn = $this->searchOpenLibrary($variation['title'], $variation['tome']);
                if ($isbn) {
                    return $isbn;
                }
            }
            
            return 'Non trouvé';
            
        } catch (\Exception $e) {
            return 'Erreur de recherche';
        }
    }

    private function generateTitleVariations($title, $tomeNumber = null)
    {
        $variations = [];
        
        // Variation 1: Ajouter "manga" si pas présent
        if (stripos($title, 'manga') === false) {
            $variations[] = [
                'title' => $title . ' manga',
                'tome' => $tomeNumber
            ];
        }
        
        // Variation 2: Ajouter "comic" pour certains titres
        $variations[] = [
            'title' => $title . ' comic',
            'tome' => $tomeNumber
        ];
        
        // Variation 3: Essayer sans certains mots
        $words = explode(' ', $title);
        if (count($words) > 2) {
            // Enlever le dernier mot (souvent un descripteur)
            $variations[] = [
                'title' => implode(' ', array_slice($words, 0, -1)),
                'tome' => $tomeNumber
            ];
        }
        
        // Variation 4: Essayer avec "graphic novel"
        $variations[] = [
            'title' => $title . ' graphic novel',
            'tome' => $tomeNumber
        ];
        
        return $variations;
    }

    private function cleanTitleForSearch($title)
    {
        // Nettoyer le titre pour améliorer la recherche
        $title = trim($title);
        
        // Extraire le numéro de tome/volume
        $tomeNumber = null;
        if (preg_match('/\b(?:Tome|Vol\.?|Volume|#)\s*(\d+)\b/i', $title, $matches)) {
            $tomeNumber = $matches[1];
        }
        
        // Supprimer les mots entre parenthèses (tomes, volumes, etc.)
        $title = preg_replace('/\s*\([^)]*\)/', '', $title);
        
        // Supprimer les numéros de tome/volume à la fin
        $title = preg_replace('/\s*(?:Tome|Vol\.?|Volume|#)\s*\d+$/i', '', $title);
        
        // Supprimer les caractères spéciaux mais garder les espaces
        $title = preg_replace('/[^\w\s\-]/', '', $title);
        
        return [
            'title' => trim($title),
            'tome' => $tomeNumber
        ];
    }

    private function searchGoogleBooks($title, $tomeNumber = null)
    {
        try {
            // Recherche avec le titre + manga
            $searchQuery = $title . " manga";
            if ($tomeNumber) {
                $searchQuery .= " tome $tomeNumber";
            }
            $url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($searchQuery) . "&maxResults=10&langRestrict=fr";
            $response = Http::timeout(10)->get($url);
            $data = $response->json();

            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $volumeInfo = $item['volumeInfo'] ?? [];
                    $title = $volumeInfo['title'] ?? '';
                    $subtitle = $volumeInfo['subtitle'] ?? '';
                    $description = $volumeInfo['description'] ?? '';
                    
                    // Vérifier si c'est bien un manga
                    $categories = $volumeInfo['categories'] ?? [];
                    $isManga = false;
                    foreach ($categories as $category) {
                        if (stripos($category, 'manga') !== false || 
                            stripos($category, 'comic') !== false || 
                            stripos($category, 'bande dessinée') !== false) {
                            $isManga = true;
                            break;
                        }
                    }
                    
                    // Vérifier dans la description aussi
                    if (stripos($description, 'manga') !== false || 
                        stripos($description, 'comic') !== false) {
                        $isManga = true;
                    }
                    
                    // Vérifier si le tome correspond
                    $tomeMatches = true;
                    if ($tomeNumber) {
                        $tomeMatches = $this->checkTomeMatch($title, $subtitle, $description, $tomeNumber);
                    }
                    
                    // Si c'est un manga, le tome correspond et qu'il a des ISBN
                    if ($isManga && $tomeMatches && isset($volumeInfo['industryIdentifiers'])) {
                        foreach ($volumeInfo['industryIdentifiers'] as $identifier) {
                            if ($identifier['type'] === 'ISBN_13' || $identifier['type'] === 'ISBN_10') {
                                $isbn = $identifier['identifier'];
                                
                                // Validation basique de l'ISBN
                                if ($this->isValidIsbn($isbn)) {
                                    return $isbn;
                                }
                            }
                        }
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function searchOpenLibrary($title, $tomeNumber = null)
    {
        try {
            // Recherche avec le titre + manga
            $searchQuery = $title . " manga";
            if ($tomeNumber) {
                $searchQuery .= " tome $tomeNumber";
            }
            $url = "https://openlibrary.org/search.json?q=" . urlencode($searchQuery) . "&limit=10&language=fre";
            $response = Http::timeout(10)->get($url);
            $data = $response->json();

            if (isset($data['docs']) && !empty($data['docs'])) {
                foreach ($data['docs'] as $doc) {
                    $title = $doc['title'] ?? '';
                    $subtitle = $doc['subtitle'] ?? '';
                    $subject = $doc['subject'] ?? [];
                    
                    // Vérifier si c'est un manga
                    $isManga = false;
                    foreach ($subject as $subj) {
                        if (stripos($subj, 'manga') !== false || 
                            stripos($subj, 'comic') !== false || 
                            stripos($subj, 'bande dessinée') !== false) {
                            $isManga = true;
                            break;
                        }
                    }
                    
                    // Vérifier si le tome correspond
                    $tomeMatches = true;
                    if ($tomeNumber) {
                        $tomeMatches = $this->checkTomeMatch($title, $subtitle, '', $tomeNumber);
                    }
                    
                    if ($isManga && $tomeMatches && isset($doc['isbn'])) {
                        $isbns = is_array($doc['isbn']) ? $doc['isbn'] : [$doc['isbn']];
                        foreach ($isbns as $isbn) {
                            if ($this->isValidIsbn($isbn)) {
                                return $isbn;
                            }
                        }
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function checkTomeMatch($title, $subtitle, $description, $tomeNumber)
    {
        $searchText = $title . ' ' . $subtitle . ' ' . $description;
        
        // Chercher le numéro de tome dans le titre/sous-titre/description
        $patterns = [
            '/\b(?:tome|vol\.?|volume|#)\s*' . $tomeNumber . '\b/i',
            '/\b' . $tomeNumber . '\s*(?:tome|vol\.?|volume)\b/i',
            '/\b' . $tomeNumber . '\b/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $searchText)) {
                return true;
            }
        }
        
        return false;
    }

    private function isValidIsbn($isbn)
    {
        // Nettoyer l'ISBN
        $isbn = preg_replace('/[^0-9X]/', '', strtoupper($isbn));
        
        // Vérifier la longueur
        if (strlen($isbn) !== 10 && strlen($isbn) !== 13) {
            return false;
        }
        
        // Validation basique : ne pas accepter des ISBN qui se répètent trop
        // ou qui semblent génériques
        $commonInvalidIsbns = [
            '9782331010057', // ISBN qui se répète
            '2723446352',    // ISBN suspect
            '9781951904388', // ISBN suspect
            '9781441107879', // ISBN-13 suspect
            '9780826429384', // ISBN-13 suspect
            '9781472595881', // ISBN-13 suspect
            '9782228934329', // ISBN-13 suspect
            '9782331029196', // ISBN-13 suspect
            '9782331029158', // ISBN-13 suspect
            '9782331029134', // ISBN-13 suspect
            '9782331029127'  // ISBN-13 suspect
        ];
        
        if (in_array($isbn, $commonInvalidIsbns)) {
            return false;
        }
        
        return true;
    }

    public function searchIsbnByTitle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $title = $request->input('title');
        $isbn = $this->findIsbnByTitle($title);

        return response()->json([
            'title' => $title,
            'isbn' => $isbn,
            'found' => $isbn !== 'Non trouvé' && $isbn !== 'Erreur de recherche'
        ]);
    }

    public function show($filename)
    {
        $path = storage_path('app/public/images/' . $filename);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        $file = file_get_contents($path);
        $type = mime_content_type($path);
        
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    public function updateMangaIsbn(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:20',
        ]);

        $title = $request->input('title');
        $newIsbn = $request->input('isbn');
        
        // Récupérer les mangas de la session
        $mangas = session('mangas', []);
        
        // Trouver et mettre à jour le manga
        foreach ($mangas as &$manga) {
            if ($manga['title'] === $title) {
                $manga['isbn'] = $newIsbn;
                $manga['isDuplicate'] = false; // Réinitialiser le flag de doublon
                break;
            }
        }
        
        // Vérifier les doublons après modification
        $duplicateIsbns = $this->detectDuplicateIsbns($mangas);
        foreach ($mangas as &$manga) {
            $manga['isDuplicate'] = in_array($manga['isbn'], $duplicateIsbns);
        }
        
        // Mettre à jour la session
        session(['mangas' => $mangas]);
        
        return response()->json([
            'success' => true,
            'message' => 'ISBN mis à jour avec succès',
            'mangas' => $mangas
        ]);
    }

    public function removeManga(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $title = $request->input('title');
        
        // Récupérer les mangas de la session
        $mangas = session('mangas', []);
        
        // Supprimer le manga
        $mangas = array_filter($mangas, function($manga) use ($title) {
            return $manga['title'] !== $title;
        });
        
        // Vérifier les doublons après suppression
        $duplicateIsbns = $this->detectDuplicateIsbns($mangas);
        foreach ($mangas as &$manga) {
            $manga['isDuplicate'] = in_array($manga['isbn'], $duplicateIsbns);
        }
        
        // Mettre à jour la session
        session(['mangas' => $mangas]);
        
        return response()->json([
            'success' => true,
            'message' => 'Manga supprimé avec succès',
            'mangas' => $mangas
        ]);
    }

    public function searchMangaPrice(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|max:20',
        ]);

        $isbn = $request->input('isbn');
        
        try {
            $priceData = $this->priceController->searchPricesOnly($isbn);
            
            return response()->json([
                'success' => true,
                'price' => $priceData['prices']['amazon'] ?? $priceData['prices']['fnac'] ?? $priceData['prices']['cultura'] ?? null,
                'prices' => $priceData['prices'],
                'occasion_price' => $priceData['occasion_price'],
                'title' => $priceData['title']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la recherche de prix: ' . $e->getMessage()
            ]);
        }
    }

    public function searchAllPrices(Request $request)
    {
        $request->validate([
            'mangas' => 'required|array',
            'mangas.*.title' => 'required|string',
            'mangas.*.isbn' => 'required|string'
        ]);

        $mangas = $request->input('mangas');
        $results = [];
        $totalPrice = 0;
        $foundPrices = 0;

        foreach ($mangas as $manga) {
            try {
                $priceData = $this->priceController->searchPricesOnly($manga['isbn']);
                
                $price = $priceData['prices']['amazon'] ?? $priceData['prices']['fnac'] ?? $priceData['prices']['cultura'] ?? null;
                
                if ($price && $price > 0) {
                    $totalPrice += $price;
                    $foundPrices++;
                    $status = 'success';
                } else {
                    $status = 'not_found';
                }

                $results[] = [
                    'title' => $manga['title'],
                    'isbn' => $manga['isbn'],
                    'price' => $price,
                    'status' => $status,
                    'all_prices' => $priceData['prices'],
                    'occasion_price' => $priceData['occasion_price']
                ];

                // Petite pause entre les requêtes
                usleep(1000000); // 1 seconde

            } catch (\Exception $e) {
                $results[] = [
                    'title' => $manga['title'],
                    'isbn' => $manga['isbn'],
                    'price' => null,
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }
        }

        // Stocker les résultats en session pour la page de résultats
        session([
            'search_results' => $results,
            'total_price' => $totalPrice,
            'found_prices' => $foundPrices,
            'total_mangas' => count($mangas)
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => route('image.search.results')
        ]);
    }

    public function showSearchResults()
    {
        $results = session('search_results', []);
        $totalPrice = session('total_price', 0);
        $foundPrices = session('found_prices', 0);
        $totalMangas = session('total_mangas', 0);

        if (empty($results)) {
            return redirect()->route('image.upload')->with('error', 'Aucun résultat de recherche trouvé.');
        }

        return view('image.search-results', compact('results', 'totalPrice', 'foundPrices', 'totalMangas'));
    }
} 