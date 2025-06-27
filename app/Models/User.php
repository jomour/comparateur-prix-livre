<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation avec l'historique des recherches.
     */
    public function historiqueSearches(): HasMany
    {
        return $this->hasMany(HistoriqueSearch::class);
    }

    /**
     * Obtenir les recherches récentes de l'utilisateur.
     */
    public function getRecentSearches($limit = 10)
    {
        return $this->historiqueSearches()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir les statistiques de recherche de l'utilisateur.
     */
    public function getSearchStats()
    {
        $totalSearches = $this->historiqueSearches()->count();
        $successfulSearches = $this->historiqueSearches()
            ->whereNotNull('prix_fnac')
            ->orWhereNotNull('prix_amazon')
            ->orWhereNotNull('prix_cultura')
            ->count();

        return [
            'total_searches' => $totalSearches,
            'successful_searches' => $successfulSearches,
            'success_rate' => $totalSearches > 0 ? round(($successfulSearches / $totalSearches) * 100, 2) : 0,
        ];
    }
}
