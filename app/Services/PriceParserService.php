<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;

class PriceParserService
{
    public function parseAmazonPrice($htmlContent)
    {
        $prices = [];
        
        // Créer un DOMDocument
        $dom = new DOMDocument();
        @$dom->loadHTML($htmlContent, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        
        // Sélecteurs XPath pour Amazon
        $xpathSelectors = [
            "//span[@class='a-price aok-align-center reinventPricePriceToPayMargin priceToPay']",
            "//span[@class='a-price-whole']",
            "//span[@class='a-price-fraction']",
            "//span[@class='a-price-symbol']",
            "//span[contains(@class, 'a-price-whole')]",
            "//span[contains(@class, 'a-price-fraction')]",
            "//span[contains(@class, 'a-price')]//span[@class='a-offscreen']",
            "//span[contains(@class, 'price')]",
            "//div[contains(@class, 'price')]"
        ];
        
        // Essayer d'extraire le prix complet d'abord
        $mainPriceSelector = "//span[@class='a-price aok-align-center reinventPricePriceToPayMargin priceToPay']";
        $mainNodes = $xpath->query($mainPriceSelector);
        
        if ($mainNodes && $mainNodes->length > 0) {
            foreach ($mainNodes as $node) {
                $wholeNodes = $xpath->query(".//span[@class='a-price-whole']", $node);
                $fractionNodes = $xpath->query(".//span[@class='a-price-fraction']", $node);
                $symbolNodes = $xpath->query(".//span[@class='a-price-symbol']", $node);
                
                $whole = '';
                $fraction = '';
                $symbol = '';
                
                if ($wholeNodes && $wholeNodes->length > 0) {
                    $whole = trim($wholeNodes->item(0)->textContent);
                }
                
                if ($fractionNodes && $fractionNodes->length > 0) {
                    $fraction = trim($fractionNodes->item(0)->textContent);
                }
                
                if ($symbolNodes && $symbolNodes->length > 0) {
                    $symbol = trim($symbolNodes->item(0)->textContent);
                }
                
                if (!empty($whole)) {
                    $priceText = $whole;
                    if (!empty($fraction)) {
                        $priceText .= ',' . $fraction;
                    }
                    if (!empty($symbol)) {
                        $priceText .= ' ' . $symbol;
                    }
                    
                    $prices[] = $priceText;
                }
            }
        }
        
        // Extraire les prix des différents formats
        $formatPriceSelectors = [
            "//span[@class='slot-price']//span[contains(@aria-label, 'à partir de')]",
            "//span[@class='slot-price']//span[@aria-label]",
            "//span[@class='slot-price']",
            "//span[@class='slot-price']//span[@class='a-size-base a-color-secondary']",
            "//span[@class='slot-price']//span[@class='a-size-base a-color-price a-color-price']",
            "//span[@class='slot-price']//span[contains(@aria-label, '€')]",
            "//span[contains(@class, 'slot-price')]//span[contains(@aria-label, '€')]",
            "//a[@class='a-button-text a-text-left']//span[@class='slot-price']//span",
            "//span[contains(@aria-label, 'à partir de')]",
            "//span[contains(@aria-label, '€')]"
        ];
        
        foreach ($formatPriceSelectors as $selector) {
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
        
        // Essayer les autres sélecteurs
        foreach ($xpathSelectors as $selector) {
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
        
        // Regex patterns
        $regexPatterns = [
            '/<span[^>]*class="[^"]*a-price[^"]*"[^>]*>.*?<span[^>]*class="[^"]*a-price-whole[^"]*"[^>]*>(\d+)<span[^>]*class="[^"]*a-price-decimal[^"]*"[^>]*>.*?<\/span><\/span><span[^>]*class="[^"]*a-price-fraction[^"]*"[^>]*>(\d+)<\/span>/is',
            '/<span[^>]*class="[^"]*slot-price[^"]*"[^>]*>.*?<span[^>]*aria-label="[^"]*à partir de (\d+[,\d]*)\s*[€&][^"]*"[^>]*>.*?<\/span>/is',
            '/<span[^>]*class="[^"]*slot-price[^"]*"[^>]*>.*?<span[^>]*aria-label="[^"]*(\d+[,\d]*)\s*€[^"]*"[^>]*>.*?<\/span>/is',
            '/<span[^>]*class="[^"]*a-size-base[^"]*"[^>]*>(\d+[,\d]*)\s*€<\/span>/is',
            '/aria-label="[^"]*(\d+[,\d]*)\s*€[^"]*"/is',
            '/aria-label="[^"]*à partir de (\d+[,\d]*)\s*[€&][^"]*"/is',
            '/<span[^>]*class="[^"]*a-price-whole[^"]*"[^>]*>(\d+)<\/span>/is',
            '/<span[^>]*class="[^"]*a-price-fraction[^"]*"[^>]*>(\d+)<\/span>/is',
            '/<span[^>]*class="[^"]*price[^"]*"[^>]*>.*?(\d+[,\d]*\s*€).*?<\/span>/is',
            '/(\d+[,\d]*)\s*€/',
            '/(\d+[,\d]*)\s*EUR/'
        ];
        
        foreach ($regexPatterns as $pattern) {
            if (preg_match_all($pattern, $htmlContent, $matches)) {
                // Vérifier si on a des captures
                if (isset($matches[0])) {
                    foreach ($matches[0] as $index => $match) {
                        // Si on a au moins 2 captures (whole et fraction)
                        if (isset($matches[1][$index]) && isset($matches[2][$index])) {
                            $priceText = $matches[1][$index] . ',' . $matches[2][$index] . ' €';
                            $prices[] = $priceText;
                        } else {
                            $prices[] = trim($match);
                        }
                    }
                }
            }
        }
        
        return $this->getMostLikelyPrice($prices);
    }

    public function parseCulturaPrice($htmlContent)
    {
        $prices = [];
        
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