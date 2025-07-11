<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;

class PriceParserService
{
    /**
     * Extrait les prix depuis les données JSON dans le HTML
     */
    private function extractPricesFromJson($htmlContent)
    {
        $prices = [];
        
        // Pattern pour trouver les scripts JSON contenant des prix
        $jsonPatterns = [
            '/<script[^>]*type="application\/json"[^>]*>(.*?)<\/script>/is',
            '/<script[^>]*type="application\/ld\+json"[^>]*>(.*?)<\/script>/is',
            '/window\.ue_sip_data\s*=\s*({.*?});/is',
            '/window\.ue_ue_id\s*=\s*"([^"]+)"/is',
            '/"price":\s*"([^"]+)"/is',
            '/"price":\s*(\d+\.?\d*)/is',
            '/"priceAmount":\s*"([^"]+)"/is',
            '/"priceAmount":\s*(\d+\.?\d*)/is'
        ];
        
        foreach ($jsonPatterns as $pattern) {
            if (preg_match_all($pattern, $htmlContent, $matches)) {
                foreach ($matches[1] as $match) {
                    // Essayer de parser le JSON
                    $jsonData = json_decode($match, true);
                    if ($jsonData) {
                        $prices = array_merge($prices, $this->extractPricesFromArray($jsonData));
                    }
                }
            }
        }
        
        // Pattern spécifique pour les prix dans les attributs data
        if (preg_match_all('/data-price="([^"]+)"/is', $htmlContent, $matches)) {
            foreach ($matches[1] as $price) {
                if (preg_match('/[\d,]+/', $price)) {
                    $prices[] = $price;
                }
            }
        }
        
        // Pattern pour les prix dans les meta tags
        if (preg_match_all('/<meta[^>]*property="product:price:amount"[^>]*content="([^"]+)"/is', $htmlContent, $matches)) {
            foreach ($matches[1] as $price) {
                if (preg_match('/[\d,]+/', $price)) {
                    $prices[] = $price;
                }
            }
        }
        
        return $prices;
    }

    /**
     * Extrait récursivement les prix d'un tableau JSON
     */
    private function extractPricesFromArray($array, $prices = [])
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $prices = $this->extractPricesFromArray($value, $prices);
            } elseif (is_string($value)) {
                // Chercher des prix dans les chaînes
                if (preg_match('/(\d+[,\d]*)\s*€/', $value, $matches)) {
                    $prices[] = $matches[1] . ' €';
                } elseif (preg_match('/(\d+[,\d]*)\s*EUR/', $value, $matches)) {
                    $prices[] = $matches[1] . ' EUR';
                }
            } elseif (is_numeric($value) && $key === 'price') {
                // Si c'est un prix numérique
                $prices[] = number_format($value, 2, ',', ' ') . ' €';
            }
        }
        
        return $prices;
    }

    /**
     * Extrait les prix d'occasion depuis le JSON de la page Cultura
     */
    private function extractUsedPricesFromJson($htmlContent)
    {
        $usedPrices = [];
        
        // Pattern pour trouver le JSON avec les données marketplace
        $jsonPattern = '/"mp_info":\s*({[^}]+"offers":\s*\[[^\]]+\][^}]+})/is';
        
        if (preg_match($jsonPattern, $htmlContent, $matches)) {
            $jsonData = $matches[1];
            
            // Nettoyer le JSON (décoder les entités HTML)
            $jsonData = html_entity_decode($jsonData);
            
            try {
                $data = json_decode($jsonData, true);
                
                if ($data && isset($data['offers'])) {
                    foreach ($data['offers'] as $offer) {
                        // Vérifier si c'est une offre d'occasion (state_code = 2)
                        if (isset($offer['state_code']) && $offer['state_code'] == 2.0) {
                            if (isset($offer['price']) && is_numeric($offer['price'])) {
                                $usedPrices[] = number_format($offer['price'], 2, ',', ' ') . ' € (occasion)';
                            }
                        }
                    }
                }
                
                // Extraire aussi les prix résumés d'occasion
                if (isset($data['lowest_price_used']['value'])) {
                    $usedPrices[] = number_format($data['lowest_price_used']['value'], 2, ',', ' ') . ' € (occasion min)';
                }
                
                if (isset($data['second_lowest_price_used']['value'])) {
                    $usedPrices[] = number_format($data['second_lowest_price_used']['value'], 2, ',', ' ') . ' € (occasion 2e)';
                }
                
            } catch (\Exception $e) {
                // En cas d'erreur de parsing JSON, essayer avec des regex
                $this->extractUsedPricesWithRegex($htmlContent, $usedPrices);
            }
        } else {
            // Si pas de JSON trouvé, essayer avec des regex
            $this->extractUsedPricesWithRegex($htmlContent, $usedPrices);
        }
        
        return $usedPrices;
    }

    /**
     * Extrait les prix d'occasion avec des regex en fallback
     */
    private function extractUsedPricesWithRegex($htmlContent, &$usedPrices)
    {
        // Patterns pour les prix d'occasion dans le JSON
        $patterns = [
            '/"lowest_price_used":\s*{\s*"value":\s*(\d+\.?\d*)/i',
            '/"second_lowest_price_used":\s*{\s*"value":\s*(\d+\.?\d*)/i',
            '/"price":\s*(\d+\.?\d*).*?"state_code":\s*2\.0/i',
            '/"total_price":\s*(\d+\.?\d*).*?"state_code":\s*2\.0/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $htmlContent, $matches)) {
                foreach ($matches[1] as $price) {
                    if (is_numeric($price)) {
                        $usedPrices[] = number_format((float)$price, 2, ',', ' ') . ' € (occasion)';
                    }
                }
            }
        }
    }

    /**
     * Extrait les prix d'occasion depuis le HTML
     */
    private function extractUsedPrices($htmlContent)
    {
        $prices = [];
        
        // Patterns pour les prix d'occasion
        $usedPricePatterns = [
            '/D\'occasion[^>]*>.*?(\d+[,\d]*)\s*€/is',
            '/D\'occasion[^>]*>.*?(\d+[,\d]*)\s*EUR/is',
            '/occasion[^>]*>.*?(\d+[,\d]*)\s*€/is',
            '/used[^>]*>.*?(\d+[,\d]*)\s*€/is',
            '/<span[^>]*class="[^"]*a-price[^"]*"[^>]*>.*?D\'occasion.*?(\d+[,\d]*)\s*€/is',
            '/<div[^>]*class="[^"]*used[^"]*"[^>]*>.*?(\d+[,\d]*)\s*€/is'
        ];
        
        foreach ($usedPricePatterns as $pattern) {
            if (preg_match_all($pattern, $htmlContent, $matches)) {
                foreach ($matches[1] as $price) {
                    if (preg_match('/[\d,]+/', $price)) {
                        $prices[] = $price . ' €';
                    }
                }
            }
        }
        
        return $prices;
    }

    public function parseCulturaPrice($htmlContent)
    {
        $prices = [];
        $usedPrices = [];
        
        // Créer un DOMDocument
        $dom = new DOMDocument();
        @$dom->loadHTML($htmlContent, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        
        // Sélecteurs pour Cultura
        $culturaSelectors = [
            "//div[@class='price color-bluelight']",
            "//div[contains(@class, 'price')]//span[@class='price__cents']",
            "//div[contains(@class, 'price')]",
            "//span[@class='price__cents']"
        ];
        
        // Essayer d'extraire le prix Cultura complet
        $mainPriceSelector = "//div[@class='price color-bluelight']";
        $mainNodes = $xpath->query($mainPriceSelector);
        
        if ($mainNodes && $mainNodes->length > 0) {
            foreach ($mainNodes as $node) {
                $text = trim($node->textContent);
                if (!empty($text) && preg_match('/[\d,]+/', $text)) {
                    $prices[] = $text;
                }
            }
        }
        
        // Essayer les autres sélecteurs
        foreach ($culturaSelectors as $selector) {
            try {
                $nodes = $xpath->query($selector);
                
                if ($nodes && $nodes->length > 0) {
                    foreach ($nodes as $node) {
                        $text = trim($node->textContent);
                        if (!empty($text) && preg_match('/[\d,]+/', $text)) {
                            $prices[] = $text;
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Regex pour Cultura
        $regexPatterns = [
            '/<div[^>]*class="[^"]*price[^"]*"[^>]*>(\d+[,\d]*)\s*<span[^>]*class="[^"]*price__cents[^"]*"[^>]*>€<\/span><\/div>/is',
            '/(\d+[,\d]*)\s*€/',
            '/(\d+[,\d]*)\s*EUR/',
            '/<div[^>]*class="[^"]*price[^"]*"[^>]*>.*?(\d+[,\d]*\s*€).*?<\/div>/is'
        ];
        
        foreach ($regexPatterns as $pattern) {
            if (preg_match_all($pattern, $htmlContent, $matches)) {
                if (isset($matches[1])) {
                    foreach ($matches[1] as $match) {
                        $prices[] = trim($match);
                    }
                }
            }
        }
        
        // Extraire les prix d'occasion depuis le JSON
        $usedPrices = $this->extractUsedPricesFromJson($htmlContent);
        
        // Si on a trouvé des prix d'occasion, les ajouter au résultat
        if (!empty($usedPrices)) {
            $prices = array_merge($prices, $usedPrices);
        }
        
        return $this->getMostLikelyPrice($prices);
    }

    public function parseFnacPrice($htmlContent)
    {
        $prices = [];
        
        // Créer un DOMDocument
        $dom = new DOMDocument();
        @$dom->loadHTML($htmlContent, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        
        // Sélecteurs pour Fnac
        $fnacSelectors = [
            "//span[@class='price']",
            "//div[@class='price']",
            "//span[contains(@class, 'price')]",
            "//div[contains(@class, 'price')]",
            "//span[@class='f-price']",
            "//div[@class='f-price']"
        ];
        
        foreach ($fnacSelectors as $selector) {
            try {
                $nodes = $xpath->query($selector);
                
                if ($nodes && $nodes->length > 0) {
                    foreach ($nodes as $node) {
                        $text = trim($node->textContent);
                        if (!empty($text) && preg_match('/[\d,]+/', $text)) {
                            $prices[] = $text;
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Regex pour Fnac
        $regexPatterns = [
            '/(\d+[,\d]*)\s*€/',
            '/(\d+[,\d]*)\s*EUR/',
            '/<span[^>]*class="[^"]*price[^"]*"[^>]*>.*?(\d+[,\d]*\s*€).*?<\/span>/is',
            '/<div[^>]*class="[^"]*price[^"]*"[^>]*>.*?(\d+[,\d]*\s*€).*?<\/div>/is'
        ];
        
        foreach ($regexPatterns as $pattern) {
            if (preg_match_all($pattern, $htmlContent, $matches)) {
                if (isset($matches[1])) {
                    foreach ($matches[1] as $match) {
                        $prices[] = trim($match);
                    }
                }
            }
        }
        
        return $this->getMostLikelyPrice($prices);
    }

    private function cleanPrice($priceText)
    {
        // Nettoyer le texte
        $priceText = trim($priceText);
        
        // Ignorer les prix qui contiennent des balises HTML
        if (strpos($priceText, '<') !== false || strpos($priceText, '>') !== false) {
            return 'Prix non trouvé';
        }
        
        // Ignorer les prix trop longs
        if (strlen($priceText) > 20) {
            return 'Prix non trouvé';
        }
        
        // Ignorer les prix qui contiennent des caractères non numériques étranges
        if (preg_match('/[^\d,\s€EUR\.]/', $priceText)) {
            return 'Prix non trouvé';
        }
        
        // Pattern pour capturer euros et centimes séparément
        if (preg_match('/(\d+)[,\s]*(\d{2})\s*€/', $priceText, $matches)) {
            $whole = $matches[1];
            $decimal = $matches[2];
            
            $value = (float)($whole . '.' . $decimal);
            if ($value < 0.01 || $value > 10000) {
                return 'Prix non trouvé';
            }
            
            return $whole . ',' . $decimal . ' €';
        }
        
        // Pattern pour prix avec virgule
        if (preg_match('/(\d+),(\d+)\s*€/', $priceText, $matches)) {
            $whole = $matches[1];
            $decimal = $matches[2];
            
            $value = (float)($whole . '.' . $decimal);
            if ($value < 0.01 || $value > 10000) {
                return 'Prix non trouvé';
            }
            
            return $whole . ',' . $decimal . ' €';
        }
        
        // Pattern pour prix entier avec €
        if (preg_match('/(\d+)\s*€/', $priceText, $matches)) {
            $whole = $matches[1];
            
            $value = (float)$whole;
            if ($value < 0.01 || $value > 10000) {
                return 'Prix non trouvé';
            }
            
            return $whole . ',00 €';
        }
        
        // Pattern pour prix sans €
        if (preg_match('/(\d+)[,\s]*(\d{2})/', $priceText, $matches)) {
            $whole = $matches[1];
            $decimal = $matches[2];
            
            $value = (float)($whole . '.' . $decimal);
            if ($value < 0.01 || $value > 10000) {
                return 'Prix non trouvé';
            }
            
            return $whole . ',' . $decimal . ' €';
        }
        
        // Pattern pour prix entier sans €
        if (preg_match('/(\d+)/', $priceText, $matches)) {
            $whole = $matches[1];
            
            $value = (float)$whole;
            if ($value < 0.01 || $value > 10000) {
                return 'Prix non trouvé';
            }
            
            return $whole . ',00 €';
        }
        
        return 'Prix non trouvé';
    }

    private function getMostLikelyPrice($prices)
    {
        if (empty($prices)) {
            return 'Prix non trouvé';
        }
        
        $cleanPrices = [];
        
        foreach ($prices as $price) {
            $cleanedPrice = $this->cleanPrice($price);
            
            if ($cleanedPrice !== 'Prix non trouvé') {
                $cleanPrices[] = $cleanedPrice;
            }
        }
        
        if (empty($cleanPrices)) {
            return 'Prix non trouvé';
        }
        
        // Retourner le premier prix valide
        return $cleanPrices[0];
    }
} 