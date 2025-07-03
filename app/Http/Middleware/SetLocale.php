<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Récupérer la langue depuis l'URL
        $locale = $request->segment(1);
        
        // Vérifier si c'est un préfixe de langue valide
        $supportedLocales = array_keys(config('languages.supported'));
        $pattern = '/^(' . implode('|', $supportedLocales) . ')$/';
        
        if (preg_match($pattern, $locale, $matches)) {
            $lang = $matches[1];
            
            // Vérifier si la langue est supportée
            if (in_array($lang, $supportedLocales)) {
                App::setLocale($lang);
                Session::put('locale', $lang);
            } else {
                // Rediriger vers la langue par défaut
                $defaultLocale = config('languages.default');
                return redirect('/' . $defaultLocale . $request->getRequestUri());
            }
        } else {
            // Si pas de préfixe de langue, rediriger vers la langue par défaut
            if (!$request->is('login') && !$request->is('forgot-password') && !$request->is('reset-password*')) {
                $defaultLocale = config('languages.default');
                return redirect('/' . $defaultLocale . $request->getRequestUri());
            }
        }

        return $next($request);
    }
} 