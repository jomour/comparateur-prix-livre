<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PriceController;
use App\Services\SeoService;
use App\Services\IsbnService;
use App\Actions\EstimateMangaPrice;
use App\Models\HistoriqueSearchLot;

class MangaLotEstimationController extends Controller
{
    protected $priceController;
    protected $isbnService;

    public function __construct(PriceController $priceController, IsbnService $isbnService)
    {
        $this->priceController = $priceController;
        $this->isbnService = $isbnService;
    }

    public function index()
    {
        // Métadonnées SEO
        $meta = SeoService::getMangaLotEstimationMeta();
        $seoType = 'website';
        
        return view('manga-lot-estimation.upload', compact('meta', 'seoType'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
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
            
            $fullPath = null;
           
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
                
                // Augmenter les limites de temps et de mémoire pour l'analyse
                set_time_limit(300); // 5 minutes
                ini_set('memory_limit', '512M');
                
                // Analyser l'image avec OpenAI
                $mangas = $this->analyzeImageWithOpenAI($fullPath);
                
                // Vérifier si l'analyse a échoué silencieusement
                if ($mangas === null) {
                    Log::error('Analyse OpenAI a retourné null - possible timeout ou erreur silencieuse');
                    return back()->with('error', 'Erreur lors de l\'analyse de l\'image. L\'analyse a pris trop de temps ou a échoué.');
                }
                
                // Nettoyer l'image après analyse (optionnel, pour économiser l'espace)
                // unlink($fullPath);
                
                return back()->with('success', __('messages.image_analyzed_successfully'))
                            ->with('image', $imageName)
                            ->with('mangas', $mangas);
                            
            } catch (\Exception $e) {
                // Nettoyer en cas d'erreur
                if ($fullPath && file_exists($fullPath)) {
                    unlink($fullPath);
                }
                
                // Log détaillé de l'erreur
                Log::error('Erreur lors du traitement de l\'image : ' . $e->getMessage());
                Log::error('Stack trace : ' . $e->getTraceAsString());
                
                // Message d'erreur plus spécifique
                $errorMessage = 'Erreur lors du traitement de l\'image.';
                if (strpos($e->getMessage(), 'timeout') !== false) {
                    $errorMessage = 'L\'analyse a pris trop de temps. Veuillez réessayer avec une image plus simple.';
                } elseif (strpos($e->getMessage(), 'memory') !== false) {
                    $errorMessage = 'Erreur de mémoire. Veuillez essayer avec une image plus petite.';
                } elseif (strpos($e->getMessage(), 'OpenAI') !== false) {
                    $errorMessage = 'Erreur de communication avec l\'IA. Veuillez réessayer.';
                }
                
                return back()->with('error', $errorMessage);
            }
        }
        
        return back()->with('error', 'Erreur lors du chargement de l\'image.');
    }

    public function uploadAjax(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Vérifications de sécurité supplémentaires
            if (!$this->isSecureImage($image)) {
                return response()->json(['error' => 'Fichier non sécurisé détecté.'], 400);
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
                return response()->json(['error' => 'Erreur de permissions sur le dossier de stockage.'], 500);
            }
            
            $fullPath = null;
            
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
                    return response()->json(['error' => 'Fichier image invalide détecté.'], 400);
                }
                
                // Augmenter les limites de temps et de mémoire pour l'analyse
                set_time_limit(300); // 5 minutes
                ini_set('memory_limit', '512M');
                
                // Analyser l'image avec OpenAI
                $mangas = $this->analyzeImageWithOpenAI($fullPath);
                
                // Vérifier si l'analyse a échoué silencieusement
                if ($mangas === null) {
                    Log::error('Analyse OpenAI a retourné null - possible timeout ou erreur silencieuse');
                    return response()->json(['error' => 'L\'analyse de l\'image a échoué. Veuillez essayer avec une image plus simple ou réessayer dans quelques instants.'], 500);
                }
                
                // Stocker les données dans la session pour l'affichage
                session([
                    'success' => __('messages.image_analyzed_successfully'),
                    'image' => $imageName,
                    'mangas' => $mangas
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => __('messages.image_analyzed_successfully'),
                    'redirect' => \App\Helpers\LocalizedRoute::url('manga.lot.estimation.upload.form')
                ]);
                            
            } catch (\Exception $e) {
                // Nettoyer en cas d'erreur
                if ($fullPath && file_exists($fullPath)) {
                    unlink($fullPath);
                }
                
                // Log détaillé de l'erreur
                Log::error('Erreur lors du traitement de l\'image : ' . $e->getMessage());
                Log::error('Stack trace : ' . $e->getTraceAsString());
                
                // Message d'erreur plus spécifique
                $errorMessage = 'Erreur lors du traitement de l\'image.';
                if (strpos($e->getMessage(), 'timeout') !== false) {
                    $errorMessage = 'L\'analyse a pris trop de temps. Veuillez réessayer avec une image plus simple.';
                } elseif (strpos($e->getMessage(), 'memory') !== false) {
                    $errorMessage = 'Erreur de mémoire. Veuillez essayer avec une image plus petite.';
                } elseif (strpos($e->getMessage(), 'OpenAI') !== false) {
                    $errorMessage = 'Erreur de communication avec l\'IA. Veuillez réessayer.';
                }
                
                return response()->json(['error' => $errorMessage], 500);
            }
        }
        
        return response()->json(['error' => 'Erreur lors du chargement de l\'image.'], 400);
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
        
        // Limiter les dimensions pour éviter les attaques DoS
        if ($width > 5000 || $height > 5000) {
            Log::warning('Dimensions d\'image trop grandes : ' . $width . 'x' . $height);
            return false;
        }
        
        // Vérifier le ratio pour détecter les images malveillantes
        $ratio = $width / $height;
        if ($ratio < 0.1 || $ratio > 10) {
            Log::warning('Ratio d\'image suspect : ' . $ratio);
            return false;
        }
        
        return true;
    }

    private function generateSecureFileName($originalName, $extension)
    {
        // Nettoyer le nom de fichier
        $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
        $cleanName = substr($cleanName, 0, 50); // Limiter la longueur
        
        // Ajouter un timestamp pour éviter les conflits
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        
        return $timestamp . '_' . $random . '_' . $cleanName;
    }

    private function isValidImageFile($filePath)
    {
        // Vérifier que le fichier existe
        if (!file_exists($filePath)) {
            return false;
        }
        
        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($mimeType, $allowedMimes)) {
            return false;
        }
        
        // Vérifier que c'est bien une image valide
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            return false;
        }
        
        return true;
    }

    private function compressImage($imagePath)
    {
        // Obtenir les informations de l'image
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            return $imagePath; // Retourner l'original si impossible de lire
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Si l'image est déjà petite (< 1MB), ne pas la compresser
        $fileSize = filesize($imagePath);
        if ($fileSize < 1024 * 1024) {
            return $imagePath;
        }
        
        // Créer une image à partir du fichier
        $image = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($imagePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($imagePath);
                break;
            default:
                return $imagePath;
        }
        
        if (!$image) {
            return $imagePath;
        }
        
        // Calculer les nouvelles dimensions (max 1200px de largeur)
        $maxWidth = 1200;
        $maxHeight = 1200;
        
        if ($width <= $maxWidth && $height <= $maxHeight) {
            imagedestroy($image);
            return $imagePath; // Pas besoin de redimensionner
        }
        
        // Calculer les nouvelles dimensions en gardant le ratio
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);
        
        // Créer la nouvelle image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Préserver la transparence pour PNG
        if ($mimeType === 'image/png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefill($newImage, 0, 0, $transparent);
        }
        
        // Redimensionner l'image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Créer le chemin du fichier compressé
        $compressedPath = $imagePath . '_compressed.jpg';
        
        // Sauvegarder l'image compressée
        $quality = 85; // Qualité JPEG
        imagejpeg($newImage, $compressedPath, $quality);
        
        // Nettoyer la mémoire
        imagedestroy($image);
        imagedestroy($newImage);
        
        return $compressedPath;
    }

    private function analyzeImageWithOpenAI($imagePath)
    {
        $maxRetries = 3;
        $retryDelay = 5; // secondes
       
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // Compresser l'image pour réduire le temps d'analyse
                $compressedImagePath = $this->compressImage($imagePath);
                
                // Encoder l'image compressée en base64
                $imageData = base64_encode(file_get_contents($compressedImagePath));
                
                // Augmenter les limites de temps pour l'analyse
                set_time_limit(300); // 5 minutes
                ini_set('memory_limit', '512M');
              
                $response = OpenAI::chat()->create([
                    'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un expert en manga et en estimation de prix. Analyse cette image de lot de manga et identifie tous les mangas visibles. Pour chaque manga, fournis OBLIGATOIREMENT : 1) Le titre exact en français 2) Le numéro de tome (si visible) 3) L\'éditeur (si visible) 4) L\'ISBN français complet (si visible sur la couverture, sinon laisse vide) 5) Une estimation de l\'état (neuf, très bon, bon, correct, usé) 6) Une estimation du prix de vente en euros. IMPORTANT : Si l\'ISBN français n\'est pas visible sur la couverture, laisse le champ "isbn" vide (""). L\'ISBN français commence généralement par 978-2-. Réponds uniquement en JSON avec cette structure : [{"title": "Titre", "tome": "1", "editor": "Éditeur", "isbn": "978-2-XXXXX-XXX-X", "condition": "bon", "estimated_price": "12.50"}, ...]'
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Analyse cette image de lot de manga et identifie tous les mangas visibles avec leurs détails.'
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
                    'max_tokens' => 1500, // Réduire pour accélérer
                    'temperature' => 0.1
                    // Supprimer le paramètre 'timeout' qui n'est pas reconnu
                ]);
                
                // Nettoyer le fichier compressé temporaire
                if ($compressedImagePath !== $imagePath && file_exists($compressedImagePath)) {
                    unlink($compressedImagePath);
                }
            
                $content = $response->choices[0]->message->content;
                
                // Nettoyer la réponse des backticks et du mot "json"
                $content = preg_replace('/```json\s*/', '', $content);
                $content = preg_replace('/```\s*$/', '', $content);
                $content = trim($content);
                
                // Essayer de parser le JSON
                $mangas = json_decode($content, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Si le JSON n'est pas valide, essayer d'extraire les informations
                    Log::warning('Réponse OpenAI non-JSON, tentative d\'extraction : ' . $content);
                    $mangas = $this->extractMangaInfoFromText($content);
                }
                
                // Valider et nettoyer les données
                if (is_array($mangas)) {
                    $mangas = array_filter($mangas, function($manga) {
                        return isset($manga['title']) && !empty($manga['title']);
                    });
                    
                    // Traiter les ISBN pour chaque manga
                    foreach ($mangas as &$manga) {
                        // Nettoyer l'ISBN si présent
                        if (isset($manga['isbn']) && !empty($manga['isbn'])) {
                            $manga['isbn'] = $this->cleanIsbn($manga['isbn']);
                            $manga['isbn_valid'] = $this->isValidIsbn($manga['isbn']);
                        } else {
                            $manga['isbn'] = '';
                            $manga['isbn_valid'] = false;
                        }
                        
                        // S'assurer que tous les champs requis sont présents
                        $manga['tome'] = $manga['tome'] ?? '';
                        $manga['editor'] = $manga['editor'] ?? '';
                        $manga['condition'] = $manga['condition'] ?? 'bon';
                        $manga['estimated_price'] = $manga['estimated_price'] ?? '5-10';
                        
                        // Combiner le titre et le tome
                        if (!empty($manga['tome'])) {
                            $manga['title'] = $manga['title'] . ' - Tome ' . $manga['tome'];
                        }
                    }
                    
                    // Rechercher automatiquement les ISBN manquants
                    $mangas = $this->enrichMangasWithIsbn($mangas);
                    
                    // Détecter les doublons d'ISBN
                    $mangas = $this->detectDuplicateIsbns($mangas);
                    
                    return $mangas;
                }
                
                return null;
                
            } catch (\Exception $e) {
                Log::error('Erreur OpenAI (tentative ' . $attempt . '/' . $maxRetries . ') : ' . $e->getMessage());
                
                if ($attempt < $maxRetries) {
                    Log::info('Nouvelle tentative dans ' . $retryDelay . ' secondes...');
                    sleep($retryDelay);
                    continue;
                } else {
                    Log::error('Toutes les tentatives ont échoué pour l\'analyse OpenAI');
                    return null;
                }
            }
        }
        
        return null;
    }

    private function extractMangaInfoFromText($text)
    {
        // Logique d'extraction basique si le JSON échoue
        $mangas = [];
        $lines = explode("\n", $text);
        
        foreach ($lines as $line) {
            if (preg_match('/(.+?)\s*-\s*Tome\s*(\d+)/i', $line, $matches)) {
                $mangas[] = [
                    'title' => trim($matches[1]),
                    'tome' => $matches[2],
                    'editor' => '',
                    'isbn' => '',
                    'condition' => 'bon',
                    'estimated_price' => '5-10'
                ];
            }
        }
        
        return $mangas;
    }

    private function isValidIsbn10($isbn)
    {
        $isbn = preg_replace('/[^0-9X]/', '', strtoupper($isbn));
        
        if (strlen($isbn) !== 10) {
            return false;
        }
        
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (10 - $i) * intval($isbn[$i]);
        }
        
        $checkDigit = $isbn[9] === 'X' ? 10 : intval($isbn[9]);
        $sum += $checkDigit;
        
        return $sum % 11 === 0;
    }

    private function detectDuplicateIsbns($mangas)
    {
        $isbnCounts = [];
        
        // Compter les occurrences de chaque ISBN
        foreach ($mangas as $manga) {
            if (!empty($manga['isbn'])) {
                $isbn = $manga['isbn'];
                if (!isset($isbnCounts[$isbn])) {
                    $isbnCounts[$isbn] = 0;
                }
                $isbnCounts[$isbn]++;
            }
        }
        
        // Marquer les doublons
        foreach ($mangas as &$manga) {
            if (!empty($manga['isbn']) && $isbnCounts[$manga['isbn']] > 1) {
                $manga['duplicate_isbn'] = true;
            }
        }
        
        return $mangas;
    }

    /**
     * Enrichit les mangas avec les ISBN manquants
     */
    private function enrichMangasWithIsbn($mangas)
    {
        foreach ($mangas as &$manga) {
            // Si l'ISBN est vide ou invalide, essayer de le trouver
            if (empty($manga['isbn']) || (isset($manga['isbn_valid']) && !$manga['isbn_valid'])) {
                // Extraire le titre et le tome
                $title = $manga['title'];
                $tome = $manga['tome'] ?? null;
                
                // Nettoyer le titre (enlever le tome si déjà dans le titre)
                $cleanTitle = $title;
                if (preg_match('/\s*-?\s*tome\s*(\d+)/i', $title, $matches)) {
                    $cleanTitle = preg_replace('/\s*-?\s*tome\s*\d+/i', '', $title);
                    $tome = $matches[1];
                }
                
                // Rechercher l'ISBN via le service
                $foundIsbn = $this->isbnService->findIsbnByTitle($cleanTitle, $tome);
                
                if ($foundIsbn) {
                    $manga['isbn'] = $foundIsbn;
                    $manga['isbn_valid'] = $this->isValidIsbn($foundIsbn);
                    $manga['isbn_auto_found'] = true; // Marquer comme trouvé automatiquement
                    
                    Log::info("ISBN trouvé automatiquement pour '{$title}': {$foundIsbn}");
                } else {
                    Log::info("Aucun ISBN trouvé pour '{$title}'");
                }
            }
        }
        
        return $mangas;
    }

    private function cleanIsbn($isbn)
    {
        // Nettoyer l'ISBN en gardant seulement les chiffres et X
        $isbn = preg_replace('/[^0-9X]/', '', strtoupper($isbn));
        
        // Formater l'ISBN avec des tirets si c'est un ISBN-13
        if (strlen($isbn) === 13) {
            return substr($isbn, 0, 3) . '-' . substr($isbn, 3, 1) . '-' . substr($isbn, 4, 4) . '-' . substr($isbn, 8, 4) . '-' . substr($isbn, 12, 1);
        }
        
        // Formater l'ISBN-10 avec des tirets
        if (strlen($isbn) === 10) {
            return substr($isbn, 0, 1) . '-' . substr($isbn, 1, 4) . '-' . substr($isbn, 5, 4) . '-' . substr($isbn, 9, 1);
        }
        
        return $isbn;
    }

    private function isValidIsbn($isbn)
    {
        // Nettoyer l'ISBN
        $isbn = preg_replace('/[^0-9X]/', '', strtoupper($isbn));
        
        // Vérifier la longueur
        if (strlen($isbn) !== 10 && strlen($isbn) !== 13) {
            return false;
        }
        
        // Validation ISBN-10
        if (strlen($isbn) === 10) {
            return $this->isValidIsbn10($isbn);
        }
        
        // Validation ISBN-13
        if (strlen($isbn) === 13) {
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += intval($isbn[$i]) * ($i % 2 === 0 ? 1 : 3);
            }
            
            $checkDigit = intval($isbn[12]);
            $calculatedCheck = (10 - ($sum % 10)) % 10;
            
            return $checkDigit === $calculatedCheck;
        }
        
        return false;
    }

    public function searchIsbnByTitle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'tome' => 'nullable|string|max:10'
        ]);
        
        $title = $request->input('title');
        $tome = $request->input('tome');
        
        $isbn = $this->isbnService->findIsbnByTitle($title, $tome);
        
        return response()->json([
            'success' => true,
            'isbn' => $isbn
        ]);
    }

    public function show($filename)
    {
        $path = storage_path('app/public/images/' . $filename);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        $mimeType = mime_content_type($path);
        
        return response()->file($path, [
            'Content-Type' => $mimeType
        ]);
    }

    public function updateMangaIsbn(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
            'isbn' => 'required|string|max:20'
        ]);
        
        $index = $request->input('index');
        $isbn = $request->input('isbn');
        
        // Récupérer les mangas de la session
        $mangas = session('mangas', []);
        
        if (isset($mangas[$index])) {
            $mangas[$index]['isbn'] = $isbn;
            $mangas[$index]['isbn_updated'] = true;
            
            session(['mangas' => $mangas]);
            
            return response()->json([
                'success' => true,
                'message' => 'ISBN mis à jour avec succès'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Manga non trouvé'
        ], 404);
    }

    public function removeManga(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0'
        ]);
        
        $index = $request->input('index');
        
        // Récupérer les mangas de la session
        $mangas = session('mangas', []);
        
        if (isset($mangas[$index])) {
            unset($mangas[$index]);
            $mangas = array_values($mangas); // Réindexer
            
            session(['mangas' => $mangas]);
            
            return response()->json([
                'success' => true,
                'message' => 'Manga supprimé avec succès'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Manga non trouvé'
        ], 404);
    }

        public function searchAllPrices(Request $request)
    {
        // Utiliser les mangas de la session plutôt que ceux du formulaire
        $mangas = session('mangas', []);
        
        if (empty($mangas)) {
            return redirect()->back()->with('error', 'Aucun manga trouvé dans la session');
        }
        
        // Créer un lot pour ce groupe de mangas
        $lot = HistoriqueSearchLot::create([
            'name' => 'Lot de ' . count($mangas) . ' mangas - ' . now()->format('d/m/Y H:i')
        ]);
        
        $results = [];
        
        // Utiliser EstimateMangaPrice pour chaque manga avec l'ID du lot
        $estimateMangaPrice = new EstimateMangaPrice(
            app(IsbnService::class),
            app(\App\Services\AnilistService::class),
            app(\App\Services\AmazonPriceParserService::class),
            app(\App\Services\CulturaPriceParserService::class),
            app(\App\Services\FnacPriceParserService::class)
        );
        
        foreach ($mangas as $index => $manga) {
            $title = $manga['title'] ?? '';
            $isbn = $manga['isbn'] ?? '';
            
            if (!empty($isbn)) {
                try {
                    $result = $estimateMangaPrice->execute($isbn, $lot->id);
                    $results[$index] = $result;
                } catch (\Exception $e) {
                    Log::error('Erreur lors de l\'estimation pour le manga ' . $index . ': ' . $e->getMessage());
                    $results[$index] = [
                        'error' => 'Erreur lors de l\'estimation',
                        'search' => null,
                        'searchData' => null,
                        'popularity' => null,
                        'rarity' => null
                    ];
                }
            } elseif (!empty($title)) {
                // Si pas d'ISBN, essayer de le trouver
                $foundIsbn = $this->isbnService->findIsbnByTitle($title);
                if ($foundIsbn) {
                    try {
                        $result = $estimateMangaPrice->execute($foundIsbn, $lot->id);
                        $results[$index] = $result;
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'estimation pour le manga ' . $index . ': ' . $e->getMessage());
                        $results[$index] = [
                            'error' => 'Erreur lors de l\'estimation',
                            'search' => null,
                            'searchData' => null,
                            'popularity' => null,
                            'rarity' => null
                        ];
                    }
                } else {
                    $results[$index] = [
                        'error' => 'ISBN non trouvé pour ce titre',
                        'search' => null,
                        'searchData' => null,
                        'popularity' => null,
                        'rarity' => null
                    ];
                }
            }
        }
        
        // Stocker les résultats dans la session pour l'affichage
        session(['estimation_results' => $results]);
       
        // Stocker les IDs historiques pour les liens "Voir détails"
        $historiqueIds = [];
        foreach ($results as $index => $result) {
            if (isset($result['search']) && $result['search'] instanceof \App\Models\HistoriqueSearch) {
                $historiqueIds[$index] = $result['search']->id;
            }
        }
        session(['historique_ids' => $historiqueIds]);
        
        // Log pour debug
        $redirectUrl = \App\Helpers\LocalizedRoute::url('resultats.recherche.image');
        
        // Rediriger vers la page de résultats avec gestion multilangue
        return redirect()->to($redirectUrl)
                        ->with('success', __('messages.analysis_completed_successfully'));
    }

    public function showSearchResults()
    {
        Log::info('ShowSearchResults - Method called');
        
        $mangas = session('mangas', []);
        $estimationResults = session('estimation_results', []);
        $historiqueIds = session('historique_ids', []);
        
        Log::info('ShowSearchResults - Mangas count: ' . count($mangas));
        Log::info('ShowSearchResults - Estimation results count: ' . count($estimationResults));
        
        if (empty($mangas)) {
            Log::info('ShowSearchResults - No mangas found, redirecting to upload form');
            return redirect()->route('manga.lot.estimation.upload.form');
        }
        
        Log::info('ShowSearchResults - Rendering view with data');
        Log::info('ShowSearchResults - Historique IDs: ' . json_encode($historiqueIds));
        return view('manga-lot-estimation.search-results', compact('mangas', 'estimationResults', 'historiqueIds'));
    }
} 