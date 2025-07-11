<?php

namespace App\Services;

interface PriceParserInterface
{
    /**
     * Recherche et parse les prix pour un identifiant donné
     * 
     * @param string $identifier ISBN ou titre
     * @param string|null $resultsPath Chemin pour sauvegarder les fichiers
     * @param string|null $searchId ID de la recherche
     * @return string|null Prix trouvé ou null si aucun prix
     */
    public function search($identifier, $resultsPath = null, $searchId = null);
} 