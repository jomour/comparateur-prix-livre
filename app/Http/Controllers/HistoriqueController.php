<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoriqueSearch;
use App\Models\HistoriqueSearchLot;
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
        $userId = auth()->id() ?? 0;
        
        // Récupérer toutes les recherches (simples et par lot)
        $allSearches = HistoriqueSearch::with(['user', 'providers', 'lot'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer tous les lots
        $allLots = HistoriqueSearchLot::with(['searches' => function($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->whereHas('searches', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Fusionner les recherches et les lots en une seule liste chronologique
        $mergedHistory = collect();
        
        // Ajouter les recherches simples
        foreach ($allSearches as $search) {
            if (!$search->lot) { // Seulement les recherches qui ne font pas partie d'un lot
                $mergedHistory->push([
                    'type' => 'search',
                    'data' => $search,
                    'created_at' => $search->created_at,
                    'title' => $search->title ?? "Manga ISBN: {$search->isbn}",
                    'isbn' => $search->isbn,
                    'manga_count' => 1
                ]);
            }
        }
        
        // Ajouter les lots
        foreach ($allLots as $lot) {
            $mergedHistory->push([
                'type' => 'lot',
                'data' => $lot,
                'created_at' => $lot->created_at,
                'title' => $lot->name,
                'isbn' => null,
                'manga_count' => $lot->searches->count()
            ]);
        }
        
        // Trier par date de création décroissante
        $mergedHistory = $mergedHistory->sortByDesc('created_at');
        
        // Paginer les résultats
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedHistory = $mergedHistory->slice($offset, $perPage);
        
        // Créer un objet de pagination personnalisé
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedHistory,
            $mergedHistory->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        // Métadonnées SEO
        $meta = SeoService::getHistoryMeta();
        $seoType = 'website';

        return view('price.historique', compact('paginator', 'meta', 'seoType'));
    }

    /**
     * Affiche les résultats d'une recherche historique
     */
    public function show($id)
    {
        $userId = auth()->id() ?? 0;
        
        // Vérifier que l'utilisateur a accès à cette recherche
        $search = HistoriqueSearch::with(['providers', 'rarityFactors'])->find($id);
        if (!$search || $search->user_id !== $userId) {
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
            'success' => true,
            'popularity_score' => $search->anilist_popularite ?? 0,
            'rating' => $search->anilist_note ?? 0,
            'status' => $search->anilist_statut ?? 'Unknown',
            'popularity_level' => $this->anilistService->getPopularityLevelFromScore($search->anilist_popularite ?? 0),
            'formatted_score' => number_format($search->anilist_popularite ?? 0, 0),
            'formatted_rating' => number_format($search->anilist_note ?? 0, 1),
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
            'results' => [], // Pas de résultats détaillés pour l'historique
            'prices' => $prices,
            'historique_id' => $search->getKey(),
            'rarity' => $rarity,
            'popularity' => $popularity,
            'meta' => $meta,
            'seoType' => $seoType,
            'structuredData' => $structuredData,
            'salesText' => $search->sales_text
        ]);
    }

    /**
     * Affiche les résultats d'un lot de mangas
     */
    public function showLot($lotId)
    {
        $userId = auth()->id() ?? 0;
        
        // Vérifier que l'utilisateur a accès à ce lot
        $lot = HistoriqueSearchLot::with(['searches' => function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->with(['providers', 'rarityFactors']);
            }])
            ->whereHas('searches', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->find($lotId);

        if (!$lot) {
            abort(403, 'Accès non autorisé');
        }

        $mangas = [];
        $estimationResults = [];
        $historiqueIds = [];

        foreach ($lot->searches as $search) {
            // Créer l'objet manga
            $manga = [
                'title' => $search->title ?? "Manga ISBN: {$search->isbn}",
                'isbn' => $search->isbn,
                'tome' => '',
                'editor' => '',
                'condition' => 'bon',
                'estimated_price' => '5-10'
            ];

            // Récupère les prix des providers pour ce search
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

            // Ajoute toutes les données propres à chaque historique + prix providers
            $historiqueData = [
                'id' => $search->id,
                'created_at' => $search->created_at,
                'updated_at' => $search->updated_at,
                'user_id' => $search->user_id,
                'title' => $search->title,
                'isbn' => $search->isbn,
                'lot' => $search->lot,
                'score_rarete' => $search->score_rarete,
                'rarete' => $search->rarete,
                'estimation_occasion_correct' => $search->estimation_occasion_correct,
                'estimation_occasion_bon' => $search->estimation_occasion_bon,
                'estimation_occasion_excellent' => $search->estimation_occasion_excellent,
                'anilist_popularite' => $search->anilist_popularite,
                'anilist_note' => $search->anilist_note,
                'anilist_statut' => $search->anilist_statut,
                'providers' => $search->providers,
                'rarityFactors' => $search->rarityFactors,
                'prices' => $prices,
            ];

            $result = [
                'search' => $search,
                'searchData' => null,
                'historique' => $historiqueData,
                'prices' => $prices,
                'popularity' => [
                    'score' => $search->anilist_popularite ?? 0,
                    'rating' => $search->anilist_note ?? 0,
                    'status' => $search->anilist_statut ?? 'Unknown',
                    'formatted_score' => number_format($search->anilist_popularite ?? 0, 0),
                    'formatted_rating' => number_format($search->anilist_note ?? 0, 1),
                ],
                'rarity' => [
                    'score' => $search->score_rarete ?? 5,
                    'explanation' => $search->rarete ?? 'Analyse de rareté non disponible',
                    'factors' => $search->rarityFactors->pluck('factor')->toArray(),
                    'value_estimation' => [
                        'correct' => $search->estimation_occasion_correct ? number_format($search->estimation_occasion_correct, 2) . '€' : 'Non disponible',
                        'bon' => $search->estimation_occasion_bon ? number_format($search->estimation_occasion_bon, 2) . '€' : 'Non disponible',
                        'excellent' => $search->estimation_occasion_excellent ? number_format($search->estimation_occasion_excellent, 2) . '€' : 'Non disponible',
                    ]
                ]
            ];

            $mangas[] = $manga;
            $estimationResults[] = $result;
            $historiqueIds[] = $search->id;
        }

        $meta = SeoService::getMangaLotEstimationMeta();
        $seoType = 'website';
        return view('manga-lot-estimation.search-results', compact('mangas', 'estimationResults', 'historiqueIds', 'meta', 'seoType'));
    }
}
