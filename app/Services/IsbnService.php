<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\HttpRequestService;

class IsbnService
{
    /**
     * Récupère le titre d'un livre via son ISBN
     */
    public function getTitleFromIsbn($isbn)
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

    /**
     * Nettoie l'ISBN en supprimant les espaces, tirets et underscores
     */
    public function cleanIsbn($isbn)
    {
        return preg_replace('/[\s\-_]/', '', $isbn);
    }

    /**
     * Vérifie si un ISBN est valide
     */
    public function isValidIsbn($isbn)
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

    /**
     * Récupère les informations complètes d'un livre via son ISBN
     */
    public function getBookInfo($isbn)
    {
        try {
            // Essayer OpenLibrary d'abord
            $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";
            $response = Http::timeout(10)->get($url);
            $data = $response->json();

            if (isset($data["ISBN:{$isbn}"])) {
                $book = $data["ISBN:{$isbn}"];
                $bookInfo = [
                    'title' => $book['title'] ?? 'Titre inconnu',
                    'author' => isset($book['authors'][0]['name']) ? $book['authors'][0]['name'] : null,
                    'publisher' => isset($book['publishers'][0]['name']) ? $book['publishers'][0]['name'] : null,
                    'published_date' => $book['publish_date'] ?? null
                ];
                
                // Si des données sont manquantes, essayer Google Books
                if ($this->hasNullValues($bookInfo)) {
                    $googleInfo = $this->getGoogleBooksInfo($isbn);
                    if ($googleInfo) {
                        $bookInfo = array_merge($bookInfo, $googleInfo);
                    }
                }
                
                return $bookInfo;
            }

            // Fallback: Google Books API
            $googleInfo = $this->getGoogleBooksInfo($isbn);
            if ($googleInfo) {
                return $googleInfo;
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Récupère les informations détaillées via Google Books API
     */
    private function getGoogleBooksInfo($isbn)
    {
        try {
            // Recherche initiale
            $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}";
            $response = Http::timeout(10)->get($url);
            $data = $response->json();

            if (!isset($data['items'][0])) {
                return null;
            }

            $item = $data['items'][0];
            $volumeInfo = $item['volumeInfo'];
            
            $bookInfo = [
                'title' => $volumeInfo['title'] ?? 'Titre inconnu',
                'author' => isset($volumeInfo['authors'][0]) ? $volumeInfo['authors'][0] : null,
                'publisher' => $volumeInfo['publisher'] ?? null,
                'published_date' => $volumeInfo['publishedDate'] ?? null,
                'page_count' => $volumeInfo['pageCount'] ?? null,
                'language' => $volumeInfo['language'] ?? null
            ];

            // Si des données sont manquantes, essayer avec le selfLink pour plus de détails
            if ($this->hasNullValues($bookInfo) && isset($item['selfLink'])) {
                $detailedResponse = Http::timeout(10)->get($item['selfLink']);
                $detailedData = $detailedResponse->json();
                
                if (isset($detailedData['volumeInfo'])) {
                    $detailedVolumeInfo = $detailedData['volumeInfo'];
                    
                    // Compléter les données manquantes
                    if (!$bookInfo['author'] && isset($detailedVolumeInfo['authors'][0])) {
                        $bookInfo['author'] = $detailedVolumeInfo['authors'][0];
                    }
                    if (!$bookInfo['publisher'] && isset($detailedVolumeInfo['publisher'])) {
                        $bookInfo['publisher'] = $detailedVolumeInfo['publisher'];
                    }
                    if (!$bookInfo['published_date'] && isset($detailedVolumeInfo['publishedDate'])) {
                        $bookInfo['published_date'] = $detailedVolumeInfo['publishedDate'];
                    }
                    if (!$bookInfo['page_count'] && isset($detailedVolumeInfo['pageCount'])) {
                        $bookInfo['page_count'] = $detailedVolumeInfo['pageCount'];
                    }
                    if (!$bookInfo['language'] && isset($detailedVolumeInfo['language'])) {
                        $bookInfo['language'] = $detailedVolumeInfo['language'];
                    }
                }
            }



            return $bookInfo;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Vérifie si un tableau contient des valeurs null
     */
    private function hasNullValues($array)
    {
        foreach ($array as $value) {
            if ($value === null) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extrait un prix numérique d'une chaîne de caractères
     */
    public function extractPriceFromString($priceString)
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

    /**
     * Retrouve l'ISBN d'un livre à partir de son titre
     */
    public function findIsbnByTitle($title, $tomeNumber = null)
    {
        $title = $this->cleanTitleForSearch($title);
        
        // Essayer plusieurs variations du titre
        $variations = $this->generateTitleVariations($title, $tomeNumber);
        
        foreach ($variations as $variation) {
            // Recherche Google Books
            $isbn = $this->searchGoogleBooks($variation['title'], $variation['tome']);
            if ($isbn) {
                return $isbn;
            }
            
            // Recherche Open Library
            $isbn = $this->searchOpenLibrary($variation['title'], $variation['tome']);
            if ($isbn) {
                return $isbn;
            }
        }
        
        return null;
    }

    /**
     * Génère des variations du titre pour améliorer la recherche
     */
    private function generateTitleVariations($title, $tomeNumber = null)
    {
        $variations = [];
        
        // Extraire le tome du titre si présent
        $cleanTitle = $title;
        $extractedTome = $tomeNumber;
        
        if (preg_match('/\s*-?\s*tome\s*(\d+)/i', $title, $matches)) {
            $cleanTitle = preg_replace('/\s*-?\s*tome\s*\d+/i', '', $title);
            $extractedTome = $matches[1];
        }
        
        // Titre original
        $variations[] = ['title' => $cleanTitle, 'tome' => $extractedTome];
        
        // Titre avec "manga" ajouté
        if (!stripos($cleanTitle, 'manga')) {
            $variations[] = ['title' => $cleanTitle . ' manga', 'tome' => $extractedTome];
        }
        
        // Titre avec "édition française"
        $variations[] = ['title' => $cleanTitle . ' édition française', 'tome' => $extractedTome];
        
        // Titre avec "Glénat" (éditeur français)
        $variations[] = ['title' => $cleanTitle . ' Glénat', 'tome' => $extractedTome];
        
        // Titre avec "bande dessinée"
        $variations[] = ['title' => $cleanTitle . ' bande dessinée', 'tome' => $extractedTome];
        
        return $variations;
    }

    /**
     * Nettoie le titre pour la recherche
     */
    private function cleanTitleForSearch($title)
    {
        // Supprimer les caractères spéciaux et normaliser
        $title = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title);
        
        return $title;
    }

    /**
     * Recherche l'ISBN via Google Books API
     */
    private function searchGoogleBooks($title, $tomeNumber = null)
    {
        try {
            $query = urlencode($title . ' manga');
            if ($tomeNumber) {
                $query .= urlencode(' tome ' . $tomeNumber);
            }
            
            $response = Http::timeout(10)->get("https://www.googleapis.com/books/v1/volumes", [
                'q' => $query,
                'langRestrict' => 'fr',
                'maxResults' => 5
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['items'])) {
                    foreach ($data['items'] as $item) {
                        if (isset($item['volumeInfo']['industryIdentifiers'])) {
                            foreach ($item['volumeInfo']['industryIdentifiers'] as $identifier) {
                                if ($identifier['type'] === 'ISBN_10' || $identifier['type'] === 'ISBN_13') {
                                    $isbn = $identifier['identifier'];
                                    
                                    // Vérifier que c'est bien un manga
                                    if ($this->isMangaBook($item['volumeInfo'])) {
                                        return $isbn;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Log silencieux pour éviter de polluer les logs
        }
        
        return null;
    }

    /**
     * Recherche l'ISBN via Open Library API
     */
    private function searchOpenLibrary($title, $tomeNumber = null)
    {
        try {
            $query = urlencode($title . ' manga');
            if ($tomeNumber) {
                $query .= urlencode(' tome ' . $tomeNumber);
            }
            
            $response = Http::timeout(10)->get("https://openlibrary.org/search.json", [
                'q' => $query,
                'language' => 'fre',
                'limit' => 5
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['docs'])) {
                    foreach ($data['docs'] as $doc) {
                        if (isset($doc['isbn'])) {
                            $isbn = is_array($doc['isbn']) ? $doc['isbn'][0] : $doc['isbn'];
                            
                            // Vérifier que c'est bien un manga
                            if ($this->isMangaBook($doc)) {
                                return $isbn;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Log silencieux pour éviter de polluer les logs
        }
        
        return null;
    }

    /**
     * Vérifie si un livre est un manga
     */
    private function isMangaBook($bookInfo)
    {
        // Vérifier les sujets/catégories
        if (isset($bookInfo['subjects'])) {
            foreach ($bookInfo['subjects'] as $subject) {
                if (stripos($subject, 'manga') !== false || 
                    stripos($subject, 'bande dessinée') !== false ||
                    stripos($subject, 'comic') !== false) {
                    return true;
                }
            }
        }
        
        // Vérifier le titre
        if (isset($bookInfo['title'])) {
            $title = strtolower($bookInfo['title']);
            if (stripos($title, 'manga') !== false || 
                stripos($title, 'bande dessinée') !== false ||
                stripos($title, 'comic') !== false) {
                return true;
            }
        }
        
        // Vérifier la description
        if (isset($bookInfo['description'])) {
            $desc = strtolower($bookInfo['description']);
            if (stripos($desc, 'manga') !== false || 
                stripos($desc, 'bande dessinée') !== false ||
                stripos($desc, 'comic') !== false) {
                return true;
            }
        }
        
        // Si on a un ISBN français (978-2-), considérer que c'est probablement un manga
        if (isset($bookInfo['industryIdentifiers'])) {
            foreach ($bookInfo['industryIdentifiers'] as $identifier) {
                if (isset($identifier['identifier']) && strpos($identifier['identifier'], '978-2-') === 0) {
                    return true;
                }
            }
        }
        
        // Plus permissif : accepter les livres avec des éditeurs français connus
        if (isset($bookInfo['publisher'])) {
            $publisher = strtolower($bookInfo['publisher']);
            if (stripos($publisher, 'glénat') !== false ||
                stripos($publisher, 'kana') !== false ||
                stripos($publisher, 'ki-oon') !== false ||
                stripos($publisher, 'pika') !== false) {
                return true;
            }
        }
        
        return false;
    }
} 