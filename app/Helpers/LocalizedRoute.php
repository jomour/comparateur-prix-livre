<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class LocalizedRoute
{
    /**
     * Génère une URL localisée
     */
    public static function localized($name, $parameters = [], $absolute = true)
    {
        $locale = App::getLocale();
        
        // S'assurer que $parameters est un tableau
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }
        
        // Si on est dans un contexte localisé, ajouter le paramètre locale
        if (request()->segment(1) && preg_match('/^([a-z]{2})$/', request()->segment(1))) {
            $parameters['locale'] = $locale;
        }
        
        $url = route($name, $parameters, $absolute);
        
        // Si l'URL ne contient pas déjà le préfixe de langue, l'ajouter
        $supportedLocales = array_keys(config('languages.supported'));
        $pattern = '/\/(' . implode('|', $supportedLocales) . ')\//';
        
        if (!preg_match($pattern, $url)) {
            $defaultLocale = config('languages.default');
            $url = str_replace('/' . $defaultLocale . '/', '/' . $locale . '/', $url);
        }
        
        return $url;
    }
    
    /**
     * Génère une URL pour changer de langue
     */
    public static function switchLanguage($locale, $currentUrl = null)
    {
        if (!$currentUrl) {
            $currentUrl = request()->getRequestUri();
        }
        
        // Remplacer le préfixe de langue actuel par le nouveau
        $supportedLocales = array_keys(config('languages.supported'));
        $pattern = '/^\/(' . implode('|', $supportedLocales) . ')\//';
        $newUrl = preg_replace($pattern, '/' . $locale . '/', $currentUrl);
        
        return $newUrl;
    }
    
    /**
     * Vérifie si la route actuelle est dans la langue spécifiée
     */
    public static function isCurrentLanguage($locale)
    {
        return App::getLocale() === $locale;
    }
    
    /**
     * Redirige vers une route localisée
     */
    public static function redirectToLocalized($routeName, $parameters = [])
    {
        $locale = App::getLocale();
        $parameters['locale'] = $locale;
        return redirect()->route($routeName, $parameters);
    }
    
    /**
     * Redirige vers la page d'accueil localisée
     */
    public static function redirectToHome()
    {
        $locale = config('languages.default');
        return redirect('/' . $locale . '/');
    }
} 