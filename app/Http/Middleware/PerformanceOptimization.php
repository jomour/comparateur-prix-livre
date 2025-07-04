<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerformanceOptimization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Ajouter les headers de performance
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Cache control pour les ressources statiques
        if ($request->is('*.css') || $request->is('*.js') || $request->is('*.png') || $request->is('*.jpg') || $request->is('*.jpeg') || $request->is('*.gif') || $request->is('*.svg')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } else {
            $response->headers->set('Cache-Control', 'public, max-age=3600');
        }

        // Compression gzip si supportée
        if (function_exists('gzencode') && !$request->is('*.css') && !$request->is('*.js')) {
            $response->headers->set('Content-Encoding', 'gzip');
        }

        return $response;
    }
} 