<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueSearchRarityFactor extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     */
    protected $table = 'historique_search_rarity_factor';

    /**
     * Les attributs qui sont assignables en masse.
     */
    protected $fillable = [
        'historique_search_id',
        'factor',
    ];

    /**
     * Relation avec HistoriqueSearch.
     */
    public function historiqueSearch(): BelongsTo
    {
        return $this->belongsTo(HistoriqueSearch::class, 'historique_search_id');
    }
}
