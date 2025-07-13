<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoriqueSearch;
use App\Services\SeoService;
use App\Services\AnilistService;

class HistoriqueController extends Controller
{
    protected $anilistService;

    public function __construct(AnilistService $anilistService)
    {
        $this->anilistService = $anilistService;
    }

    /**
     * Affiche l'historique des recherches de l'utilisateur connecté
     */
    public function index()
    {
        $searches = HistoriqueSearch::with(['user', 'providers'])
            ->where('user_id', auth()->id() ?? 0)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Métadonnées SEO
        $meta = SeoService::getHistoryMeta();
        $seoType = 'website';

        return view('price.historique', compact('searches', 'meta', 'seoType'));
    }

    /**
     * Affiche les résultats d'une recherche historique
     */
    public function show($id)
    {
        // Vérifier que l'utilisateur a accès à cette recherche
        $search = HistoriqueSearch::with(['providers', 'rarityFactors'])->find($id);
        if (!$search || $search->user_id !== (auth()->id() ?? 0)) {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer le titre via l'ISBN (ou utiliser un titre par défaut)
        $title = $search->title ?? "Manga ISBN: {$search->isbn}";

        // Construire les prix à partir des providers
        $prices = [];
        foreach ($search->providers as $provider) {
            if ($provider->min > 0) {
                $prices[$provider->name] = [
                    'min' => $provider->min,
                    'max' => $provider->max,
                    'amplitude' => $provider->amplitude,
                    'average' => $provider->average,
                    'count' => $provider->nb_offre,
                    'formatted_min' => number_format($provider->min, 2, ',', ' '),
                    'formatted_max' => number_format($provider->max, 2, ',', ' '),
                    'formatted_average' => number_format($provider->average, 2, ',', ' '),
                    'formatted_amplitude' => number_format($provider->amplitude, 2, ',', ' '),
                ];
            } else {
                $prices[$provider->name] = __('messages.price_not_found');
            }
        }

        // Construire les données de rareté
        $rarity = [
            'score' => $search->score_rarete ?? 5,
            'explanation' => $search->rarete ?? 'Analyse de rareté non disponible',
            'factors' => $search->rarityFactors->pluck('factor')->toArray(),
            'value_estimation' => [
                'correct' => $search->estimation_occasion_correct ? number_format($search->estimation_occasion_correct, 2) . '€' : 'Non disponible',
                'bon' => $search->estimation_occasion_bon ? number_format($search->estimation_occasion_bon, 2) . '€' : 'Non disponible',
                'excellent' => $search->estimation_occasion_excellent ? number_format($search->estimation_occasion_excellent, 2) . '€' : 'Non disponible',
            ]
        ];

        // Construire les données de popularité AniList
        $popularity = [
            'success' => !is_null($search->anilist_popularite) || !is_null($search->anilist_note) || !is_null($search->anilist_statut),
            'popularity_score' => $search->anilist_popularite ?? 0,
            'rating' => $search->anilist_note ?? 0,
            'popularity_level' => $this->anilistService->getPopularityLevelFromScore($search->anilist_popularite ?? 0),
            'status' => $search->anilist_statut ?? 'UNKNOWN',
            'error' => null
        ];

        // Métadonnées SEO pour les résultats
        $meta = SeoService::getResultsMeta($search->isbn, $title, $prices);
        $seoType = 'product';
        $structuredData = SeoService::getStructuredData('product', [
            'title' => $title,
            'isbn' => $search->isbn,
            'prices' => $prices
        ]);

        return view('price.results', [
            'isbn' => $search->isbn,
            'title' => $title,
            'results' => [], // Pas de résultats HTML stockés
            'prices' => $prices,
            'historique_id' => $search->id,
            'rarity' => $rarity,
            'popularity' => $popularity,
            'meta' => $meta,
            'seoType' => $seoType,
            'structuredData' => $structuredData
        ]);
    }

}
