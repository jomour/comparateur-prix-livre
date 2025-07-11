<?php

namespace App\Services;

use App\ValueObjects\PriceStats;

class CulturaPriceParserService implements PriceParserInterface
{
    /**
     * Récupère et parse les prix Cultura pour un ISBN donné
     */
    public function search($isbn, $resultsPath = null, $searchId = null)
    {
        $searchUrl = "https://www.cultura.com/search/results?search_query=" . $isbn;

         $httpRequestService = new \App\Services\HttpRequestService();
         $filename = "index_amazon_{$searchId}.html";
         $culturaHtml = $httpRequestService->fetchAndStore($searchUrl, $resultsPath ?: sys_get_temp_dir(), $filename)['content'];
        
         // Récupérer tous les prix via l'URL AJAX
        $allPrices = $this->getAllCulturaPrices($isbn, $culturaHtml, $resultsPath, $searchId);
        return $allPrices;
        
        try {
            
            // Si on a trouvé une URL de produit, parser aussi les prix d'occasion
            if ($productUrl && $resultsPath && $searchId) {
                $usedMangaFile = $resultsPath . '/index_used_manga_' . $searchId . '.html';
                if (file_exists($usedMangaFile)) {
                    $productHtml = file_get_contents($usedMangaFile);
                    
                    // Extraire le product_sku pour l'API GraphQL
                    $productSku = $this->extractProductSku($productHtml);
                    
                    if ($productSku) {
                        // Récupérer tous les prix d'occasion via l'API GraphQL
                        $graphqlPrices = $this->getUsedPricesFromGraphQL($productSku, $apiKey);
                        
                        if (empty($graphqlPrices)) {
                           return [];
                        }
                        return $graphqlPrices;

                    }
                }
            }
            
        } catch (\Exception $e) {
            $errorHtml = '<html><body>Erreur lors de la récupération Cultura</body></html>';
            
            if ($resultsPath && $searchId) {
                $culturaFile = $resultsPath . '/index_cultura_' . $searchId . '.html';
                file_put_contents($culturaFile, $errorHtml);
                chmod($culturaFile, 0644);
            }
            
            return [];
        }
    }

    private function getAllCulturaPrices($isbn, $culturaHtml, $resultsPath = null, $searchId = null)
    {
        $productUrl = $this->extractProductUrl($culturaHtml);

        if ($productUrl) {
            $httpRequestService = new \App\Services\HttpRequestService();
            $filename = 'index_used_cultura_manga_' . $searchId . '.html';
            $productCulturaHtml = $httpRequestService->fetchAndStore($productUrl, $resultsPath ?: sys_get_temp_dir(), $filename)['content'];
            
             // Extraire le product_sku pour l'API GraphQL
             $productSku = $this->extractProductSku($productCulturaHtml);
                    
             if ($productSku) {
                 // Récupérer tous les prix d'occasion via l'API GraphQL
                 $graphqlPrices = $this->getUsedPricesFromGraphQL($productSku);
                
                 if (empty($graphqlPrices)) {
                    return [];
                 }
                 return $graphqlPrices;
        
            }
        }
        return [];
    }

    /**
     * Extrait l'URL du produit depuis le HTML Cultura
     */
    private function extractProductUrl($htmlContent)
    {
        // Pattern pour extraire les URLs de produits Cultura
        // Format: /p-nom-du-produit-isbn.html
        $urlPattern = '/href="(\/p-[^"]+\.html)"/i';
        
        if (preg_match($urlPattern, $htmlContent, $matches)) {
            $relativeUrl = $matches[1];
            return 'https://www.cultura.com' . $relativeUrl;
        }
        
        // Pattern alternatif pour les liens de produits
        $altUrlPattern = '/<a[^>]*class="[^"]*one-product[^"]*"[^>]*href="([^"]+)"/i';
        
        if (preg_match($altUrlPattern, $htmlContent, $matches)) {
            $url = $matches[1];
            // Si l'URL est relative, la rendre absolue
            if (strpos($url, '/') === 0) {
                return 'https://www.cultura.com' . $url;
            }
            return $url;
        }
        
        return null;
    }

    /**
     * Extrait le product_sku depuis le HTML (robuste)
     */
    private function extractProductSku($htmlContent)
    {
        // 1. Cherche dans les JSON bruts (guillemets simples ou doubles)
        if (preg_match('/product_sku["\']?\s*:\s*["\']?([0-9A-Za-z_-]+)["\']?/i', $htmlContent, $matches)) {
            return $matches[1];
        }

        // 2. Cherche dans les data-* ou attributs HTML
        if (preg_match('/data-product-sku=["\']?([0-9A-Za-z_-]+)["\']?/i', $htmlContent, $matches)) {
            return $matches[1];
        }

        // 3. Cherche dans tous les blocs <script> ou <script type="application/ld+json">
        if (preg_match_all('/<script[^>]*>(.*?)<\/script>/is', $htmlContent, $scripts)) {
            foreach ($scripts[1] as $script) {
                if (preg_match('/product_sku["\']?\s*:\s*["\']?([0-9A-Za-z_-]+)["\']?/i', $script, $matches)) {
                    return $matches[1];
                }
                // Essaye de parser le JSON
                $json = json_decode($script, true);
                if (is_array($json) && isset($json['product_sku'])) {
                    return $json['product_sku'];
                }
            }
        }

        // 4. Fallback : cherche un nombre à 7 chiffres (format Cultura) dans le HTML
        if (preg_match('/\b([0-9]{7})\b/', $htmlContent, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Récupère les prix d'occasion via l'API GraphQL
     */
    private function getUsedPricesFromGraphQL($productSku)
    {   
        $graphqlUrl = "https://www.cultura.com/magento/graphql";
        
        $query = 'query ($product_sku: String) {
            mpOffers(product_sku: $product_sku) {
                offer_id
                product_sku
                quantity
                shop {
                    name
                    id
                    url_key
                    grade
                    evaluations_count
                }
                ranking
                price
                total_price
                origin_price
                state_code
            }
        }';

        $variables = ['product_sku' => $productSku];
        
        $postData = [
            'query' => $query,
            'variables' => $variables
        ];
        
        try {
            $ch = curl_init($graphqlUrl);
            
            // Headers pour simuler un navigateur réel
            $headers = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept: application/json, text/plain, */*',
                'Accept-Language: fr-FR,fr;q=0.9,en;q=0.8',
                'Accept-Encoding: gzip, deflate, br',
                'Content-Type: application/json',
                'Origin: https://www.cultura.com',
                'Referer: https://www.cultura.com/',
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: same-origin',
                'Cache-Control: no-cache',
                'Pragma: no-cache'
            ];
            
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => 'gzip, deflate'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($response === false) {
                throw new \Exception('Erreur cURL: ' . curl_error($ch));
            }
            
            curl_close($ch);
            
            // Vérifier si c'est du HTML (captcha)
            if (strpos($response, '<html') !== false) {
                return [];
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }
            
            $usedPrices = [];
            
            if (isset($data['data']['mpOffers'])) {
                foreach ($data['data']['mpOffers'] as $offer) {
                    // Vérifier si c'est une offre d'occasion (state_code = 1, 2 ou 3)
                    if (isset($offer['state_code']) && in_array($offer['state_code'], [1, 2, 3])) {
                        // Utiliser origin_price au lieu de price pour les prix d'occasion
                        if (isset($offer['origin_price']) && is_numeric($offer['origin_price'])) {
                            $usedPrices[] = (float)$offer['origin_price'];
                        } elseif (isset($offer['price']) && is_numeric($offer['price'])) {
                            // Fallback vers price si origin_price n'existe pas
                            $usedPrices[] = (float)$offer['price'];
                        }
                    }
                }
            }
            
            // Trier les prix du plus petit au plus grand
            sort($usedPrices);
            
            // Retourner les statistiques complètes
            if (!empty($usedPrices)) {
                return new PriceStats($usedPrices);
            }
            
            return [];
            
        } catch (\Exception $e) {
            return [];
        }
    }
} 