<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueSearchProvider extends Model
{
    use HasFactory;

    protected $table = 'historique_search_provider';

    protected $fillable = [
        'historique_search_id',
        'name',
        'min',
        'max',
        'amplitude',
        'average',
        'nb_offre',
    ];

    protected $casts = [
        'min' => 'decimal:2',
        'max' => 'decimal:2',
        'amplitude' => 'decimal:2',
        'average' => 'decimal:2',
        'nb_offre' => 'integer',
    ];

    /**
     * Relation avec HistoriqueSearch
     */
    public function historiqueSearch()
    {
        return $this->belongsTo(HistoriqueSearch::class, 'historique_search_id');
    }

    /**
     * Scope pour filtrer par nom de fournisseur
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Scope pour les fournisseurs avec des prix valides
     */
    public function scopeWithValidPrices($query)
    {
        return $query->whereNotNull('min')->where('min', '>', 0);
    }

    /**
     * Obtenir le prix formaté minimum
     */
    public function getFormattedMinAttribute()
    {
        return $this->min ? number_format($this->min, 2, ',', ' ') : null;
    }

    /**
     * Obtenir le prix formaté maximum
     */
    public function getFormattedMaxAttribute()
    {
        return $this->max ? number_format($this->max, 2, ',', ' ') : null;
    }

    /**
     * Obtenir le prix formaté moyen
     */
    public function getFormattedAverageAttribute()
    {
        return $this->average ? number_format($this->average, 2, ',', ' ') : null;
    }

    /**
     * Obtenir l'amplitude formatée
     */
    public function getFormattedAmplitudeAttribute()
    {
        return $this->amplitude ? number_format($this->amplitude, 2, ',', ' ') : null;
    }
}
