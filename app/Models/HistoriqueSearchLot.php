<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueSearchLot extends Model
{
    use HasFactory;

    protected $table = 'historique_search_lot';

    protected $fillable = [
        'name'
    ];

    /**
     * Relation avec les recherches historiques
     */
    public function searches()
    {
        return $this->hasMany(HistoriqueSearch::class, 'lot');
    }
}
