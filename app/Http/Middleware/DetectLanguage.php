<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DetectLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Next): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // Si on est déjà sur une route localisée, ne rien faire
        if (str_starts_with($request->path(), 'fr/') || str_starts_with($request->path(), 'en/')) {
            return $next($request);
        }
        
        // Détecter la langue du navigateur
        $browserLocale = $request->getPreferredLanguage(['fr', 'en']);
        
        // Si aucune langue n'est détectée, utiliser l'anglais par défaut
        if (!$browserLocale) {
            $browserLocale = 'en';
        }
        
        // Si on est sur la route racine, rediriger vers la version localisée
        if ($request->path() === '/') {
            return redirect('/' . $browserLocale . '/');
        }
        
        return $next($request);
    }
} 