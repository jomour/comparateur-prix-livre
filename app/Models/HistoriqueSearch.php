<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'prix_amazon',
        'prix_cultura',
        'prix_fnac',
        'estimation_occasion',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
     * Obtenir le meilleur prix parmi les trois fournisseurs.
     */
    public function getBestPriceAttribute()
    {
        $prices = [];
        
        if ($this->prix_amazon !== 'Prix non trouvé') {
            $prices['amazon'] = (float) str_replace(['€', ' '], '', $this->prix_amazon);
        }
        if ($this->prix_cultura !== 'Prix non trouvé') {
            $prices['cultura'] = (float) str_replace(['€', ' '], '', $this->prix_cultura);
        }
        if ($this->prix_fnac !== 'Prix non trouvé') {
            $prices['fnac'] = (float) str_replace(['€', ' '], '', $this->prix_fnac);
        }

        if (empty($prices)) {
            return null;
        }

        $minPrice = min($prices);
        $provider = array_search($minPrice, $prices);

        return [
            'price' => number_format($minPrice, 2) . '€',
            'provider' => $provider
        ];
    }

    /**
     * Vérifier si tous les prix ont été trouvés.
     */
    public function getHasAllPricesAttribute()
    {
        return $this->prix_amazon !== 'Prix non trouvé' && 
               $this->prix_cultura !== 'Prix non trouvé' && 
               $this->prix_fnac !== 'Prix non trouvé';
    }

    /**
     * Obtenir le nombre de prix trouvés.
     */
    public function getPricesFoundAttribute()
    {
        $count = 0;
        if ($this->prix_amazon !== 'Prix non trouvé') $count++;
        if ($this->prix_cultura !== 'Prix non trouvé') $count++;
        if ($this->prix_fnac !== 'Prix non trouvé') $count++;
        return $count;
    }
}
