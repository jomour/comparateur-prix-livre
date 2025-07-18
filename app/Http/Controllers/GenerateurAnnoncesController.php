<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GenerateurAnnoncesController extends Controller
{
    public function index()
    {
        return view('generateur-annonces.upload');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Sauvegarder l'image
            $imagePath = $request->file('image')->store('temp', 'public');
            $fullPath = storage_path('app/public/' . $imagePath);

            // Encoder l'image en base64
            $imageData = base64_encode(file_get_contents($fullPath));

            // Vérifier que la clé API OpenAI est configurée
            $apiKey = config('openai.api_key');
            if (!$apiKey) {
                Storage::disk('public')->delete($imagePath);
                return redirect()->back()->withErrors(['image' => 'Configuration OpenAI manquante. Veuillez contacter l\'administrateur.']);
            }

            // Appel à GPT-4o via l'API OpenAI
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert en analyse d\'images. Tu dois analyser l\'image fournie et décrire l\'objet principal visible. Si tu vois plusieurs objets distincts, tu dois signaler une erreur. Si tu ne vois aucun objet clairement identifiable, tu dois aussi signaler une erreur. Si tu vois un seul objet clairement identifiable, tu dois produire un texte de vente d\'occasion SEO pour cet objet. Le texte doit être attractif, détaillé et destiné à être publié sur un site d\'annonce d\'occasion. Inclus les détails visibles,et  l\'état apparent de l\'objet visible. Pas de prix suggérée.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyse cette image et produits un texte de vente d\'occasion pour l\'objet principal visible. Si tu vois plusieurs objets ou aucun objet clair, réponds avec "ERREUR: Multiple objects detected" ou "ERREUR: No object detected".'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:image/jpeg;base64,' . $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7
            ]);

            // Nettoyer le fichier temporaire
            Storage::disk('public')->delete($imagePath);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'];

                // Vérifier si c'est une erreur
                if (str_contains($content, 'ERREUR:')) {
                    $errorMessage = str_replace('ERREUR:', '', $content);
                    if (str_contains($content, 'Multiple objects detected')) {
                        $errorMessage = 'Plusieurs objets détectés dans l\'image. Veuillez prendre une photo avec un seul objet clairement visible.';
                    } elseif (str_contains($content, 'No object detected')) {
                        $errorMessage = 'Aucun objet clairement identifiable détecté dans l\'image. Veuillez prendre une photo plus claire avec un objet bien visible.';
                    }
                    return redirect()->back()->withErrors(['image' => $errorMessage]);
                }

                // Sauvegarder l'annonce générée dans l'historique si l'utilisateur est connecté
                if (Auth::check()) {
                    // TODO: Créer un modèle pour sauvegarder les annonces générées
                }

                // Rediriger vers la page de résultats avec l'annonce générée
                return redirect()->route('fr.generateur.annonces.results')->with([
                    'annonce' => $content,
                    'image_path' => $imagePath
                ]);

            } else {
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                $errorMessage = 'Erreur lors de la génération de l\'annonce.';
                if ($response->status() === 401) {
                    $errorMessage = 'Erreur d\'authentification OpenAI. Veuillez contacter l\'administrateur.';
                } elseif ($response->status() === 429) {
                    $errorMessage = 'Limite de requêtes OpenAI atteinte. Veuillez réessayer dans quelques minutes.';
                } elseif ($response->status() === 500) {
                    $errorMessage = 'Erreur serveur OpenAI. Veuillez réessayer plus tard.';
                }

                return redirect()->back()->withErrors(['image' => $errorMessage]);
            }

        } catch (\Exception $e) {
            Log::error('Generateur annonces error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'Erreur lors du traitement de l\'image.';
            if (str_contains($e->getMessage(), 'cURL error 28')) {
                $errorMessage = 'Délai d\'attente dépassé. La génération prend trop de temps, veuillez réessayer.';
            } elseif (str_contains($e->getMessage(), 'SSL')) {
                $errorMessage = 'Erreur de connexion sécurisée. Veuillez réessayer.';
            }

            return redirect()->back()->withErrors(['image' => $errorMessage]);
        }
    }

    public function showResults()
    {
        if (!session()->has('annonce')) {
            return redirect()->route('fr.generateur.annonces');
        }

        return view('generateur-annonces.results', [
            'annonce' => session('annonce'),
            'image_path' => session('image_path')
        ]);
    }
} 