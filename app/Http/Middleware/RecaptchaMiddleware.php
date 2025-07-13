<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RecaptchaMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST')) {
            $recaptchaToken = $request->input('g-recaptcha-response');
            
            if (!$recaptchaToken) {
                Log::warning('reCAPTCHA token missing', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->url()
                ]);
                
                // Retourner une réponse JSON au lieu d'une redirection
                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.recaptcha_required')
                    ], 422);
                }
                
                return back()->withErrors(['recaptcha' => __('messages.recaptcha_required')]);
            }
            
            try {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $recaptchaToken,
                    'remoteip' => $request->ip(),
                ]);
                
                $result = $response->json();
                
                Log::info('reCAPTCHA verification result', [
                    'success' => $result['success'] ?? false,
                    'score' => $result['score'] ?? 0,
                    'action' => $result['action'] ?? 'unknown',
                    'ip' => $request->ip()
                ]);
                
                if (!$result['success']) {
                    Log::warning('reCAPTCHA verification failed', [
                        'errors' => $result['error-codes'] ?? [],
                        'ip' => $request->ip()
                    ]);
                    
                    // Gérer spécifiquement l'erreur timeout-or-duplicate
                    if (isset($result['error-codes']) && in_array('timeout-or-duplicate', $result['error-codes'])) {
                        Log::info('reCAPTCHA timeout-or-duplicate error - allowing request to proceed');
                        
                        // Pour timeout-or-duplicate, permettre la requête de continuer
                        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                            return response()->json([
                                'success' => true,
                                'message' => 'Request allowed despite reCAPTCHA timeout'
                            ]);
                        }
                        
                        return $next($request);
                    }
                    
                    // Retourner une réponse JSON au lieu d'une redirection
                    if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json([
                            'success' => false,
                            'message' => __('messages.recaptcha_failed')
                        ], 422);
                    }
                    
                    return back()->withErrors(['recaptcha' => __('messages.recaptcha_failed')]);
                }
                
                // Pour reCAPTCHA v3, vérifier le score (0.0 = bot, 1.0 = humain)
                if (isset($result['score']) && $result['score'] < 0.5) {
                    Log::warning('reCAPTCHA score too low', [
                        'score' => $result['score'],
                        'ip' => $request->ip()
                    ]);
                    
                    // Retourner une réponse JSON au lieu d'une redirection
                    if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json([
                            'success' => false,
                            'message' => __('messages.recaptcha_failed')
                        ], 422);
                    }
                    
                    return back()->withErrors(['recaptcha' => __('messages.recaptcha_failed')]);
                }
                
            } catch (\Exception $e) {
                Log::error('reCAPTCHA verification error', [
                    'error' => $e->getMessage(),
                    'ip' => $request->ip()
                ]);
                
                // Retourner une réponse JSON au lieu d'une redirection
                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.recaptcha_error')
                    ], 422);
                }
                
                return back()->withErrors(['recaptcha' => __('messages.recaptcha_error')]);
            }
        }
        
        return $next($request);
    }
} 