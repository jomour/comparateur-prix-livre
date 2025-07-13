<?php

namespace App\Services;

class HttpRequestService
{
    /**
     * Effectue une requête HTTP avec les headers appropriés et sauvegarde le contenu
     */
    public function fetchAndStore($url, $resultsPath, $filename, $headers = [])
    {
        // Headers plus réalistes pour simuler un navigateur humain
        $defaultHeaders = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Cache-Control: max-age=0',
            'Sec-Ch-Ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'DNT: 1'
        ];

        // Fusionner les headers par défaut avec ceux fournis
        $finalHeaders = array_merge($defaultHeaders, $headers);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 120, // Timeout plus long pour les requêtes lentes
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $finalHeaders,
            CURLOPT_ENCODING => 'gzip, deflate',
            CURLOPT_COOKIEJAR => '/tmp/cookies.txt', // Gestion des cookies
            CURLOPT_COOKIEFILE => '/tmp/cookies.txt',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_REFERER => 'https://www.google.com/', // Référent pour simuler une recherche Google
            CURLOPT_AUTOREFERER => true,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        ]);

        // Ajouter un délai aléatoire pour simuler un humain
        usleep(rand(500000, 2000000)); // 0.5 à 2 secondes

        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($content === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('Erreur cURL: ' . $error);
        }
        
        curl_close($ch);

        // Vérifier si on a reçu une page d'erreur Amazon
        if (strpos($content, 'Toutes nos excuses') !== false || strpos($content, 'Une erreur de système interne') !== false) {
            throw new \Exception('Amazon a détecté la requête automatisée. Protection anti-bot activée.');
        }

        // Sauvegarder le contenu dans un fichier
        $filePath = $resultsPath . '/' . $filename;
        file_put_contents($filePath, $content);
        chmod($filePath, 0644);

        return [
            'file_path' => $filePath,
            'content' => $content,
            'http_code' => $httpCode
        ];
    }

    /**
     * Effectue une requête HTTP simple sans sauvegarde
     */
    public function fetch($url, $headers = [])
    {
        // Headers plus réalistes pour simuler un navigateur humain
        $defaultHeaders = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Cache-Control: max-age=0',
            'Sec-Ch-Ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'DNT: 1'
        ];

        $finalHeaders = array_merge($defaultHeaders, $headers);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $finalHeaders,
            CURLOPT_ENCODING => 'gzip, deflate',
            CURLOPT_COOKIEJAR => '/tmp/cookies.txt',
            CURLOPT_COOKIEFILE => '/tmp/cookies.txt',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_REFERER => 'https://www.google.com/',
            CURLOPT_AUTOREFERER => true,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        ]);

        // Délai aléatoire
        usleep(rand(500000, 2000000));

        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($content === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('Erreur cURL: ' . $error);
        }
        
        curl_close($ch);

        // Vérifier si on a reçu une page d'erreur Amazon
        if (strpos($content, 'Toutes nos excuses') !== false || strpos($content, 'Une erreur de système interne') !== false) {
            throw new \Exception('Amazon a détecté la requête automatisée. Protection anti-bot activée.');
        }

        return [
            'content' => $content,
            'http_code' => $httpCode
        ];
    }

    /**
     * Effectue une requête avec rotation de User-Agent
     */
    public function fetchWithRotatingUserAgent($url, $resultsPath = null, $filename = null)
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Edge/120.0.0.0'
        ];

        $randomUserAgent = $userAgents[array_rand($userAgents)];
        
        $headers = [
            'User-Agent: ' . $randomUserAgent
        ];

        if ($resultsPath && $filename) {
            return $this->fetchAndStore($url, $resultsPath, $filename, $headers);
        } else {
            return $this->fetch($url, $headers);
        }
    }
} 