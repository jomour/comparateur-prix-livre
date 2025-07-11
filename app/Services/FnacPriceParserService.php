<?php

namespace App\Services;

use App\ValueObjects\PriceStats;

class FnacPriceParserService implements PriceParserInterface
{
    /**
     * Récupère et parse les prix Fnac pour un titre donné
     */
    public function search($title, $resultsPath = null, $searchId = null)
    {
        $searchUrl = "https://www.fnac.com/SearchResult/ResultList.aspx?SCat=0!1&Search=" . urlencode($title) . "&sft=1&sa=0";

        $httpRequestService = new \App\Services\HttpRequestService();
        $filename = "index_fnac_{$searchId}.html";
        $culturaHtml = $httpRequestService->fetchAndStore($searchUrl, $resultsPath ?: sys_get_temp_dir(), $filename)['content'];
        
         // Récupérer tous les prix via l'URL AJAX
        $allPrices = $this->getAllCulturaPrices($culturaHtml, $title, $resultsPath, $searchId);
        return $allPrices;
    }

    /**
     * Parse le prix depuis le HTML Fnac
     */
    private function getAllCulturaPrices($htmlContent, $title, $resultsPath = null, $searchId = null)
    {
        // Parser le texte de la description du premier article
        $descriptionText = $this->parseFirstArticleDescription($htmlContent);
       
        if(!$this->verifyTitleMatch($descriptionText, $title)){
            return [];
        }
        
        // Récupérer les prix d'occasion via le popup
        $usedMangaHtml = $this->getUsedMangaHtml($htmlContent, $resultsPath, $searchId);
        
        if(empty($usedMangaHtml)){
            return [];
        }

        $allPrices = $this->extractUsedFnacPricePopup($usedMangaHtml);
       
        // Calculer les statistiques complètes
        return new PriceStats($allPrices);
    }
    
    /**
     * Récupère le HTML des offres d'occasion
     */
    private function getUsedMangaHtml($htmlContent, $resultsPath = null, $searchId = null)
    {
        // Extraire l'URL du produit depuis le HTML
        $productUrl = $this->extractProductUrl($htmlContent);
        
        if (!$productUrl) {
            return '';
        }

        // Construire l'URL pour les offres d'occasion
        $usedUrl = $productUrl . "?Used=1";
        
        $httpRequestService = new \App\Services\HttpRequestService();
        $filename = "index_fnac_used_{$searchId}.html";
        $usedHtml = $httpRequestService->fetchAndStore($usedUrl, $resultsPath ?: sys_get_temp_dir(), $filename)['content'];
        
        return $usedHtml;
    }
    
    /**
     * Extrait l'URL du produit depuis le HTML Fnac
     */
    private function extractProductUrl($htmlContent)
    {
        // Pattern pour extraire l'URL du premier produit (ancien format)
        if (preg_match('/<a[^>]*class="[^"]*Article-title[^"]*"[^>]*href="([^"]*FilterId=3[^"]*)"/i', $htmlContent, $matches)) {
            $url = $matches[1];
            // Si l'URL est relative, la rendre absolue
            if (strpos($url, '/') === 0) {
                return 'https://www.fnac.com' . $url;
            }
            return $url;
        }
        
        // Pattern pour extraire l'URL API depuis le bouton "plus d'offres" (nouveau format)
        if (preg_match('/<button[^>]*class="[^"]*f-faMpMoreOffers__link[^"]*"[^>]*data-src="([^"]*FilterId=3[^"]*)"/i', $htmlContent, $matches)) {
            $apiUrl = $matches[1];
            // Si l'URL est relative, la rendre absolue
            if (strpos($apiUrl, '/') === 0) {
                return 'https://www.fnac.com' . $apiUrl;
            }
            return $apiUrl;
        }
        
        return null;
    }
    

    private function extractUsedFnacPricePopup($htmlContent)
    {
        if (empty($htmlContent)) {
            return [];
        }
        // Remplacement de la logique de pagination par un comptage des offres
        preg_match_all('/<span class="f-offerList__itemPrice">\s*([0-9]+,[0-9]+)\s*€\s*<\/span>/', $htmlContent, $prixMatches);
        $prixList = array_map(function($p) { return floatval(str_replace(',', '.', $p)); }, $prixMatches[1]);
        sort($prixList, SORT_NUMERIC);
        return $prixList;
    }
    
    /**
     * Parse le texte de la description du premier article
     */
    private function parseFirstArticleDescription($htmlContent)
    {
        // Rechercher le premier élément <p class="Article-desc">
        if (preg_match('/<p class="Article-desc">(.*?)<\/p>/s', $htmlContent, $matches)) {
            $descriptionHtml = $matches[1];
            
            // Extraire le texte en supprimant les balises HTML
            $descriptionText = strip_tags($descriptionHtml);
            
            // Nettoyer le texte (supprimer les espaces multiples, etc.)
            $descriptionText = preg_replace('/\s+/', ' ', trim($descriptionText));
            
            return $descriptionText;
        }
        
        return null;
    }
    
    /**
     * Vérifie si le texte contient au moins 80% des mots du titre recherché
     */
    private function verifyTitleMatch($descriptionText, $searchTitle)
    {
        if (!$descriptionText || !$searchTitle) {
            return false;
        }

        $descriptionNormalized = $this->normalizeText($descriptionText);
        $searchTitleNormalized = $this->normalizeText($searchTitle);

        $searchWords = array_filter(explode(' ', $searchTitleNormalized));
        if (empty($searchWords)) {
            return false;
        }

        foreach ($searchWords as $word) {
            if (strlen($word) > 2 && strpos($descriptionNormalized, $word) === false) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Normalise un texte (minuscules, suppression des accents)
     */
    private function normalizeText($text)
    {
        // Convertir en minuscules
        $text = strtolower($text);
        
        // Remplacer les caractères accentués
        $text = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ'],
            ['a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y'],
            $text
        );
        
        // Supprimer les caractères spéciaux
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        
        return $text;
    }
} 