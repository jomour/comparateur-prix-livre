<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class HistoriqueSearch extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     */
    protected $table = 'historique_search';

    /**
     * Les attributs qui sont assignables en masse.
     */
    protected $fillable = [
        'user_id',
        'isbn',
        'title',
        'estimation_occasion_correct',
        'estimation_occasion_bon',
        'estimation_occasion_excellent',
        'rarete',
        'score_rarete',
        'anilist_popularite',
        'anilist_note',
        'anilist_statut',
        'lot',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'estimation_occasion_correct' => 'decimal:2',
        'estimation_occasion_bon' => 'decimal:2',
        'estimation_occasion_excellent' => 'decimal:2',
        'score_rarete' => 'integer',
        'anilist_popularite' => 'decimal:2',
        'anilist_note' => 'decimal:2',
    ];

    /**
     * Relation avec l'utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le lot.
     */
    public function lot(): BelongsTo
    {
        return $this->belongsTo(HistoriqueSearchLot::class, 'lot');
    }

    /**
     * Relation avec les providers de prix.
     */
    public function providers(): HasMany
    {
        return $this->hasMany(HistoriqueSearchProvider::class, 'historique_search_id');
    }

    /**
     * Relation avec les facteurs de rareté.
     */
    public function rarityFactors(): HasMany
    {
        return $this->hasMany(HistoriqueSearchRarityFactor::class, 'historique_search_id');
    }

    /**
     * Obtenir le provider Amazon.
     */
    public function amazonProvider()
    {
        return $this->providers()->where('name', 'amazon')->first();
    }

    /**
     * Obtenir le provider Cultura.
     */
    public function culturaProvider()
    {
        return $this->providers()->where('name', 'cultura')->first();
    }

    /**
     * Obtenir le provider Fnac.
     */
    public function fnacProvider()
    {
        return $this->providers()->where('name', 'fnac')->first();
    }

    /**
     * Obtenir le chemin du fichier HTML Amazon.
     */
    public function getAmazonFilePathAttribute()
    {
        return storage_path("app/public/results/index_amazon_{$this->id}.html");
    }

    /**
     * Obtenir le chemin du fichier HTML Cultura.
     */
    public function getCulturaFilePathAttribute()
    {
        return storage_path("app/public/results/index_cultura_{$this->id}.html");
    }

    /**
     * Obtenir le chemin du fichier HTML Fnac.
     */
    public function getFnacFilePathAttribute()
    {
        return storage_path("app/public/results/index_fnac_{$this->id}.html");
    }

    /**
     * Vérifier si le fichier Amazon existe.
     */
    public function getAmazonFileExistsAttribute()
    {
        return file_exists($this->amazon_file_path);
    }

    /**
     * Vérifier si le fichier Cultura existe.
     */
    public function getCulturaFileExistsAttribute()
    {
        return file_exists($this->cultura_file_path);
    }

    /**
     * Vérifier si le fichier Fnac existe.
     */
    public function getFnacFileExistsAttribute()
    {
        return file_exists($this->fnac_file_path);
    }

    /**
     * Obtenir le contenu du fichier HTML Amazon.
     */
    public function getAmazonFileContent()
    {
        if ($this->amazon_file_exists) {
            return file_get_contents($this->amazon_file_path);
        }
        return null;
    }

    /**
     * Obtenir le contenu du fichier HTML Cultura.
     */
    public function getCulturaFileContent()
    {
        if ($this->cultura_file_exists) {
            return file_get_contents($this->cultura_file_path);
        }
        return null;
    }

    /**
     * Obtenir le contenu du fichier HTML Fnac.
     */
    public function getFnacFileContent()
    {
        if ($this->fnac_file_exists) {
            return file_get_contents($this->fnac_file_path);
        }
        return null;
    }

    /**
     * Scope pour filtrer par utilisateur.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour filtrer par ISBN.
     */
    public function scopeForIsbn($query, $isbn)
    {
        return $query->where('isbn', $isbn);
    }

    /**
     * Scope pour les recherches récentes.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Obtenir le meilleur prix parmi les providers.
     */
    public function getBestPriceAttribute()
    {
        $prices = [];
        
        foreach ($this->providers as $provider) {
            if ($provider->min > 0) {
                $prices[$provider->name] = $provider->min;
            }
        }

        if (empty($prices)) {
            return null;
        }

        $minPrice = min($prices);
        $providerName = array_search($minPrice, $prices);

        return [
            'price' => number_format($minPrice, 2) . '€',
            'provider' => $providerName
        ];
    }

    /**
     * Vérifier si tous les prix ont été trouvés.
     */
    public function getHasAllPricesAttribute()
    {
        return $this->providers()->where('min', '>', 0)->count() === 3;
    }

    /**
     * Obtenir le nombre de prix trouvés.
     */
    public function getPricesFoundAttribute()
    {
        return $this->providers()->where('min', '>', 0)->count();
    }

    /**
     * Obtenir les estimations d'occasion formatées.
     */
    public function getEstimationOccasionFormattedAttribute()
    {
        return [
            'correct' => $this->estimation_occasion_correct ? number_format($this->estimation_occasion_correct, 2) . '€' : 'Non disponible',
            'bon' => $this->estimation_occasion_bon ? number_format($this->estimation_occasion_bon, 2) . '€' : 'Non disponible',
            'excellent' => $this->estimation_occasion_excellent ? number_format($this->estimation_occasion_excellent, 2) . '€' : 'Non disponible',
        ];
    }

    /**
     * Obtenir les données AniList formatées.
     */
    public function getAnilistDataFormattedAttribute()
    {
        return [
            'popularite' => $this->anilist_popularite ? number_format($this->anilist_popularite, 1) : 'Non disponible',
            'note' => $this->anilist_note ? number_format($this->anilist_note, 1) . '/100' : 'Non disponible',
            'statut' => $this->anilist_statut ?: 'Non disponible',
        ];
    }

    /**
     * Vérifier si les données d'estimation sont disponibles.
     */
    public function getHasEstimationDataAttribute()
    {
        return !is_null($this->estimation_occasion_correct) || 
               !is_null($this->estimation_occasion_bon) || 
               !is_null($this->estimation_occasion_excellent);
    }

    /**
     * Vérifier si les données AniList sont disponibles.
     */
    public function getHasAnilistDataAttribute()
    {
        return !is_null($this->anilist_popularite) || 
               !is_null($this->anilist_note) || 
               !is_null($this->anilist_statut);
    }
}
