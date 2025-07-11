<?php

namespace App\Services;

use App\ValueObjects\PriceStats;

class AmazonPriceParserService implements PriceParserInterface
{
    /**
     * Récupère et parse les prix Amazon pour un ISBN donné
     */
    public function search($isbn, $resultsPath = null, $searchId = null)
    {
        // Construire l'URL de recherche Amazon
        $searchUrl = "https://www.amazon.fr/s?k=" . urlencode($isbn);

        $httpRequestService = new \App\Services\HttpRequestService();
        $filename = "index_amazon_{$searchId}.html";    
        $amazonHtml = $httpRequestService->fetchAndStore($searchUrl, $resultsPath ?: sys_get_temp_dir(), $filename)['content'];
        
        // Récupérer tous les prix via l'URL AJAX
        $allPrices = $this->getAllAmazonPrices($isbn, $amazonHtml, $resultsPath, $searchId);
        return $allPrices;
    }

    /**
     * Récupère tous les prix Amazon via l'URL AJAX et retourne le prix moyen sans les extrêmes
     */
    public function getAllAmazonPrices($isbn, $amazonHtml, $resultsPath = null, $searchId = null)
    {
        // Extraire l'ASIN de la page Amazon
        $asin = $this->extractAsinFromAmazonPage($amazonHtml);
        
        if (!$asin) {
            throw new \Exception("ASIN non trouvé sur la page Amazon");
        }
        
        // Construire l'URL AJAX
        $ajaxUrl = $this->buildAmazonAjaxUrl($asin);
        
        $httpRequestService = new \App\Services\HttpRequestService();
        $filename = "index_amazon_used_manga_{$searchId}.html";
        $ajaxContent = $httpRequestService->fetchAndStore($ajaxUrl, $resultsPath ?: sys_get_temp_dir(), $filename)['content'];
       
        // Extraire tous les prix d'occasion
        return $this->extractAmazonUsedPrice($ajaxContent);
    }

    /**
     * Extrait l'ASIN de la page Amazon
     */
    private function extractAsinFromAmazonPage($html)
    {
        // Pattern pour trouver l'ASIN dans les attributs data-csa-c-asin
        if (preg_match('/data-csa-c-asin="([A-Z0-9]{10})"/', $html, $matches)) {
            return $matches[1];
        }
        
        // Pattern alternatif pour l'ASIN dans les liens
        if (preg_match('/\/dp\/([A-Z0-9]{10})/', $html, $matches)) {
            return $matches[1];
        }
        
        // Pattern pour l'ASIN dans les inputs cachés
        if (preg_match('/<input[^>]*name="ASIN"[^>]*value="([A-Z0-9]{10})"/', $html, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Construit l'URL AJAX Amazon pour récupérer tous les prix
     */
    private function buildAmazonAjaxUrl($asin)
    {
        return "https://www.amazon.fr/gp/product/ajax/ref=aod_f_used?" . 
               http_build_query([
                   'asin' => $asin,
                   'm' => '',
                   'qid' => '',
                   'smid' => '',
                   'sourcecustomerorglistid' => '',
                   'sourcecustomerorglistitemid' => '',
                   'sr' => '',
                   'pc' => 'dp',
                   'experienceId' => 'aodAjaxMain'
               ]);
    }

    /**
     * Extrait tous les prix d'occasion du contenu AJAX Amazon et retourne un objet avec statistiques complètes
     */
    public function extractAmazonUsedPrice($html)
    {
        $prices = [];
        
        // Pattern pour extraire les prix des éléments d'occasion
        // Chercher les prix dans les spans avec id="aod-price-*"
        if (preg_match_all('/<span id="aod-price-(\d+)"[^>]*>.*?<span class="aok-offscreen">\s*([0-9,]+)\s*€\s*<\/span>/s', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $price = (float) str_replace(',', '.', $match[2]);
                $prices[] = $price;
            }
        }
        
        // Pattern alternatif pour les prix dans les spans avec class="a-price"
        if (empty($prices) && preg_match_all('/<span class="a-price[^"]*"[^>]*>.*?<span class="a-price-whole">([0-9]+)<span class="a-price-decimal">,<\/span><\/span><span class="a-price-fraction">([0-9]+)<\/span><span class="a-price-symbol">€<\/span>/s', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $price = (float) ($match[1] . '.' . $match[2]);
                $prices[] = $price;
            }
        }
        
        // Pattern pour les prix d'occasion spécifiquement
        if (empty($prices)) {
            // Chercher les éléments avec "D'occasion" et extraire les prix associés
            if (preg_match_all('/<div id="aod-offer-heading"[^>]*>.*?<span[^>]*>\s*D\'occasion[^<]*<\/span>.*?<span id="aod-price-(\d+)"[^>]*>.*?<span class="aok-offscreen">\s*([0-9,]+)\s*€\s*<\/span>/s', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $price = (float) str_replace(',', '.', $match[2]);
                    $prices[] = $price;
                }
            }
        }
        
        // Filtrer pour exclure les prix "Neuf" et ne garder que les prix d'occasion
        $filteredPrices = [];
        if (preg_match_all('/<div id="aod-offer-heading"[^>]*>.*?<span[^>]*>\s*(D\'occasion|Neuf)[^<]*<\/span>.*?<span id="aod-price-(\d+)"[^>]*>.*?<span class="aok-offscreen">\s*([0-9,]+)\s*€\s*<\/span>/s', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $condition = trim($match[1]);
                $price = (float) str_replace(',', '.', $match[3]);
                
                // Ne garder que les prix d'occasion
                if ($condition === "D'occasion") {
                    $filteredPrices[] = $price;
                }
            }
        }
        
        // Utiliser les prix filtrés si on en a trouvé
        if (!empty($filteredPrices)) {
            $prices = $filteredPrices;
        }
        if (count($prices) == 0) {
            return [];
        }
        
        return new PriceStats($prices);
    }
} 