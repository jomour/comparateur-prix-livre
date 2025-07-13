<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class AnilistService
{
    /**
     * Récupère les données de popularité d'un manga depuis AniList
     */
    public function getMangaPopularity($title, $isbn)
    {
        // Utilise ChatGPT pour obtenir le nom de la série à partir du titre
        try {
            $cleanTitle = $this->extractSeriesName($title);
        } catch (\Exception $e) {
            // fallback: nettoyage basique si l'API échoue
            $cleanTitle = $this->cleanTitleBasic($title);
        }
        
        try {
            $query = $this->getAnilistQuery();
            
            $data = [
                'query' => $query,
                'variables' => ['search' => $cleanTitle]
            ];
            
            $response = $this->makeAnilistRequest($data);
            
            if ($response === false) {
                return $this->getErrorResponse('Erreur cURL');
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->getErrorResponse('Erreur JSON: ' . json_last_error_msg());
            }
            
            if (isset($data['data']['Page']['media']) && !empty($data['data']['Page']['media'])) {
                $manga = $data['data']['Page']['media'][0]; // Premier résultat
                
                return $this->formatAnilistResponse($manga, $title);
            }
            
            return $this->getErrorResponse('Aucun manga trouvé sur AniList');
            
        } catch (\Exception $e) {
            return $this->getErrorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Extrait le nom de la série à partir du titre complet
     */
    private function extractSeriesName($title)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "Tu es un assistant qui extrait le nom de la série d'un titre de manga. Tu dois répondre uniquement par le nom de la série, sans aucune explication, ni ponctuation superflue, ni numéro de tome, ni autre information."
                ],
                [
                    'role' => 'user',
                    'content' => "Voici un titre de manga (qui peut contenir le nom de la série, le numéro de tome, etc) : \"$title\". Donne-moi uniquement le nom de la série, et rien d'autre."
                ]
            ],
            'max_tokens' => 20,
            'temperature' => 0.1
        ]);
        
        return trim($response->choices[0]->message->content);
    }

    /**
     * Nettoyage basique du titre si l'API OpenAI échoue
     */
    private function cleanTitleBasic($title)
    {
        $cleanTitle = preg_replace('/\b(tome|vol(?:ume)?|num(éro)?)\b[\s.:_-]*\d*/iu', '', $title);
        $cleanTitle = preg_replace('/\d+/u', '', $cleanTitle);
        $cleanTitle = trim(preg_replace('/\s{2,}/', ' ', $cleanTitle));
        
        return $cleanTitle;
    }

    /**
     * Retourne la requête GraphQL pour AniList
     */
    private function getAnilistQuery()
    {
        return "
        query (\$search: String) {
            Page(page: 1, perPage: 5) {
                media(search: \$search, type: MANGA) {
                    id
                    title {
                        romaji
                        english
                        native
                    }
                    averageScore
                    meanScore
                    popularity
                    trending
                    favourites
                    status
                    format
                    genres
                    description
                    coverImage {
                        large
                        medium
                    }
                    rankings {
                        rank
                        type
                        format
                        year
                        season
                    }
                    stats {
                        scoreDistribution {
                            score
                            amount
                        }
                    }
                }
            }
        }";
    }

    /**
     * Effectue la requête HTTP vers AniList
     */
    private function makeAnilistRequest($data)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://graphql.anilist.co',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            return false;
        }
        
        if ($httpCode !== 200) {
            return false;
        }
        
        return $response;
    }

    /**
     * Formate la réponse AniList
     */
    private function formatAnilistResponse($manga, $title)
    {
        // Récupérer les données
        $popularity = $manga['popularity'] ?? 0;
        $averageScore = $manga['averageScore'] ?? 0;
        $meanScore = $manga['meanScore'] ?? 0;
        $favourites = $manga['favourites'] ?? 0;
        $trending = $manga['trending'] ?? 0;
        
        // Calculer un score de popularité normalisé (0-100)
        $popularityScore = $this->calculatePopularityScore($popularity, $favourites, $trending);
        
        // Calculer la note moyenne
        $rating = $averageScore > 0 ? $averageScore : $meanScore;
        
        // Déterminer le niveau de popularité basé sur le score normalisé
        $popularityLevel = $this->getPopularityLevelFromScore($popularityScore);
        
        return [
            'success' => true,
            'popularity_score' => $popularityScore,
            'rating' => $rating,
            'popularity_level' => $popularityLevel,
            'status' => $manga['status'] ?? 'UNKNOWN',
            'raw_data' => [
                'popularity' => $popularity,
                'average_score' => $averageScore,
                'mean_score' => $meanScore,
                'favourites' => $favourites,
                'trending' => $trending,
                'title' => $manga['title']['english'] ?? $manga['title']['romaji'] ?? $title,
                'status' => $manga['status'] ?? 'UNKNOWN',
                'format' => $manga['format'] ?? 'UNKNOWN',
                'genres' => $manga['genres'] ?? []
            ]
        ];
    }

    /**
     * Calcule le score de popularité normalisé
     */
    private function calculatePopularityScore($popularity, $favourites, $trending)
    {
        // Normaliser les valeurs
        $popScore = min(100, ($popularity / 1000) * 100); // Popularité max ~1000
        $favScore = min(100, ($favourites / 50000) * 100); // Favoris max ~50000
        $trendScore = min(100, ($trending / 1000) * 100); // Trending max ~1000
        
        // Pondération: popularité 50%, favoris 30%, trending 20%
        $weightedScore = ($popScore * 0.5) + ($favScore * 0.3) + ($trendScore * 0.2);
        
        return round($weightedScore, 1);
    }

    /**
     * Détermine le niveau de popularité basé sur le score normalisé (0-100)
     */
    public function getPopularityLevelFromScore($score)
    {
        if ($score >= 80) return '🔥 Très populaire';
        if ($score >= 60) return '📈 Populaire';
        if ($score >= 40) return '➡️ Modéré';
        if ($score >= 20) return '📉 Peu populaire';
        return '❌ Très peu populaire';
    }

    /**
     * Retourne une réponse d'erreur formatée
     */
    private function getErrorResponse($error)
    {
        return [
            'success' => false,
            'error' => $error,
            'popularity_score' => 0,
            'rating' => 0,
            'popularity_level' => 'Non disponible',
            'status' => 'UNKNOWN'
        ];
    }
} 