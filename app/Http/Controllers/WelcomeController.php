<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SeoService;
use Illuminate\Support\Facades\App;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        // Si on est sur la route racine sans préfixe de langue, détecter et rediriger
        if ($request->path() === '/') {
            // Détecter la langue du navigateur
            $browserLocale = $request->getPreferredLanguage(['fr', 'en']);
            
            // Si aucune langue n'est détectée, utiliser l'anglais par défaut
            if (!$browserLocale) {
                $browserLocale = 'en';
            }
            
            // Rediriger vers la version localisée appropriée
            return redirect('/' . $browserLocale . '/');
        }
        
        // Si on est déjà sur une route localisée, afficher la vue
        $meta = SeoService::getKeywordSpecificMeta('landing-page');
        
        return view('landing', compact('meta'));
    }
} 