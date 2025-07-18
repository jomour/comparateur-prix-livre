<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\MangaLotEstimationController;
use App\Http\Controllers\SitemapController;
use App\Helpers\LocalizedRoute;

// Routes d'authentification (sans préfixe de langue)
Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

Route::get('/login', function () {
    return redirect()->route('login');
});

// Routes SEO
Route::get('/sitemap.xml', [SitemapController::class, 'generate'])->name('sitemap.xml');
Route::get('/sitemap-index.xml', [SitemapController::class, 'index'])->name('sitemap.index');

// Routes françaises optimisées SEO
Route::prefix('fr')->middleware('setlocale')->group(function () {
    
    // Route racine française
    Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('fr.welcome');
    
    // Routes principales françaises avec URLs SEO (publiques)
    Route::get('/dashboard', function () {
        if (Auth::check()) {
            return redirect()->route('fr.comparateur.prix');
        } else {
            return redirect()->route('fr.comparateur.prix');
        }
    })->name('fr.dashboard');
    
    // Comparateur de prix manga - URLs SEO françaises (publiques)
    Route::get('/comparateur-prix-manga', [PriceController::class, 'index'])->name('fr.comparateur.prix');
    Route::get('/prix-manga', [PriceController::class, 'index'])->name('fr.prix.manga');
    Route::get('/comparateur-prix-livres', [PriceController::class, 'index'])->name('fr.comparateur.livres');
    Route::get('/economiser-manga', [PriceController::class, 'index'])->name('fr.economiser.manga');
    Route::get('/meilleur-prix-manga', [PriceController::class, 'index'])->name('fr.meilleur.prix');
    
    // Actions de recherche (publiques avec reCAPTCHA)
    Route::post('/comparateur-prix-manga/recherche', [PriceController::class, 'search'])->name('fr.comparateur.recherche')->middleware('recaptcha');
    Route::post('/prix-manga/recherche', [PriceController::class, 'search'])->name('fr.prix.recherche')->middleware('recaptcha');
    Route::post('/verifier-isbn', [PriceController::class, 'verifyIsbn'])->name('fr.verifier.isbn');
    Route::get('/prix-manga/resultats', [PriceController::class, 'showResults'])->name('fr.prix.resultats');
    
    // Routes anglaises pour les résultats
    Route::get('/manga-prices/results', [PriceController::class, 'showResults'])->name('en.manga.prices.results');
    
    // Historique des recherches (authentifiées)
    Route::middleware('auth')->group(function () {
        Route::get('/historique-recherches', [HistoriqueController::class, 'index'])->name('fr.historique.recherches');
        Route::get('/historique-prix', [HistoriqueController::class, 'index'])->name('fr.historique.prix');
        Route::get('/mes-recherches', [HistoriqueController::class, 'index'])->name('fr.mes.recherches');
        Route::get('/historique-recherches/{id}', [HistoriqueController::class, 'show'])->name('fr.historique.show');
        Route::get('/historique-lots/{lotId}', [HistoriqueController::class, 'showLot'])->name('fr.historique.show.lot');
        
        // Recherche par image (authentifiée)
        Route::get('/estimation-lot-manga', [MangaLotEstimationController::class, 'index'])->name('fr.estimation.lot.manga');
        Route::get('/recherche-photo', [MangaLotEstimationController::class, 'index'])->name('fr.recherche.photo');
        Route::post('/upload-image', [MangaLotEstimationController::class, 'upload'])->name('fr.upload.image');
        Route::post('/upload-image-ajax', [MangaLotEstimationController::class, 'uploadAjax'])->name('fr.upload.image.ajax');
        Route::post('/recherche-isbn-image', [MangaLotEstimationController::class, 'searchIsbnByTitle'])->name('fr.recherche.isbn.image');
        Route::post('/recherche-prix-image', [MangaLotEstimationController::class, 'searchPrice'])->name('fr.recherche.prix.image');
        Route::post('/recherche-tous-prix', [MangaLotEstimationController::class, 'searchAllPrices'])->name('fr.recherche.tous.prix');
        Route::post('/mettre-a-jour-isbn', [MangaLotEstimationController::class, 'updateMangaIsbn'])->name('fr.mettre.a.jour.isbn');
        Route::post('/supprimer-manga', [MangaLotEstimationController::class, 'removeManga'])->name('fr.supprimer.manga');
        Route::get('/resultats-recherche-image', [MangaLotEstimationController::class, 'showSearchResults'])->name('fr.resultats.recherche.image');
        Route::get('/resultats-estimation-lot', [MangaLotEstimationController::class, 'showSearchResults'])->name('fr.resultats.estimation.lot');
        
        // Analyse d'images avec GPT-4o (authentifiée)
        Route::get('/generateur-annonces', [App\Http\Controllers\GenerateurAnnoncesController::class, 'index'])->name('fr.generateur.annonces');
Route::post('/generateur-annonces', [App\Http\Controllers\GenerateurAnnoncesController::class, 'generate'])->name('fr.generateur.annonces.generate');
Route::get('/resultats-generateur-annonces', [App\Http\Controllers\GenerateurAnnoncesController::class, 'showResults'])->name('fr.generateur.annonces.results');
        
        // Profil utilisateur
        Route::get('/mon-profil', [ProfileController::class, 'edit'])->name('fr.mon.profil');
        Route::patch('/mon-profil', [ProfileController::class, 'update'])->name('fr.mon.profil.update');
        Route::delete('/mon-profil', [ProfileController::class, 'destroy'])->name('fr.mon.profil.destroy');
    });
    
    // Route publique pour afficher les images
    Route::get('/afficher-image/{filename}', [MangaLotEstimationController::class, 'show'])->name('fr.afficher.image');
    
    // Redirections
    Route::get('/register', function () {
        return redirect()->route('login')->with('status', __('messages.registration_disabled'));
    })->name('fr.register');
    
    Route::post('/register', function () {
        return redirect()->route('login')->with('status', __('messages.registration_disabled'));
    });
});

// Routes anglaises optimisées SEO
Route::prefix('en')->middleware('setlocale')->group(function () {
    
    // Route racine anglaise
    Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('en.welcome');
    
    // Routes principales anglaises avec URLs SEO (publiques)
    Route::get('/dashboard', function () {
        if (Auth::check()) {
            return redirect()->route('en.manga.price.comparator');
        } else {
            return redirect()->route('en.manga.price.comparator');
        }
    })->name('en.dashboard');
    
    // Manga price comparator - URLs SEO anglaises (publiques)
    Route::get('/manga-price-comparator', [PriceController::class, 'index'])->name('en.manga.price.comparator');
    Route::get('/manga-prices', [PriceController::class, 'index'])->name('en.manga.prices');
    Route::get('/manga-book-price-comparison', [PriceController::class, 'index'])->name('en.manga.book.comparison');
    Route::get('/save-money-manga', [PriceController::class, 'index'])->name('en.save.money.manga');
    Route::get('/best-manga-price', [PriceController::class, 'index'])->name('en.best.manga.price');
    Route::get('/manga-price-checker', [PriceController::class, 'index'])->name('en.manga.price.checker');
    
    // Search actions (publiques avec reCAPTCHA)
    Route::post('/manga-price-comparator/search', [PriceController::class, 'search'])->name('en.manga.price.search')->middleware('recaptcha');
    Route::post('/manga-prices/search', [PriceController::class, 'search'])->name('en.manga.prices.search')->middleware('recaptcha');
    Route::post('/verify-isbn', [PriceController::class, 'verifyIsbn'])->name('en.verify.isbn');
    
    // Search history (authentifiée)
    Route::middleware('auth')->group(function () {
        Route::get('/search-history', [HistoriqueController::class, 'index'])->name('en.search.history');
        Route::get('/price-history', [HistoriqueController::class, 'index'])->name('en.price.history');
        Route::get('/my-searches', [HistoriqueController::class, 'index'])->name('en.my.searches');
        Route::get('/search-history/{id}', [HistoriqueController::class, 'show'])->name('en.historique.show');
        Route::get('/search-lots/{lotId}', [HistoriqueController::class, 'showLot'])->name('en.historique.show.lot');
        
        // Image search (authentifiée)
        Route::get('/manga-lot-estimation', [MangaLotEstimationController::class, 'index'])->name('en.manga.lot.estimation');
        Route::get('/photo-search', [MangaLotEstimationController::class, 'index'])->name('en.photo.search');
        Route::post('/upload-image', [MangaLotEstimationController::class, 'upload'])->name('en.upload.image');
        Route::post('/upload-image-ajax', [MangaLotEstimationController::class, 'uploadAjax'])->name('en.upload.image.ajax');
        Route::post('/search-isbn-image', [MangaLotEstimationController::class, 'searchIsbnByTitle'])->name('en.search.isbn.image');
        Route::post('/search-price-image', [MangaLotEstimationController::class, 'searchPrice'])->name('en.search.price.image');
        Route::post('/search-all-prices', [MangaLotEstimationController::class, 'searchAllPrices'])->name('en.search.all.prices');
        Route::post('/update-isbn', [MangaLotEstimationController::class, 'updateMangaIsbn'])->name('en.update.isbn');
        Route::post('/remove-manga', [MangaLotEstimationController::class, 'removeManga'])->name('en.remove.manga');
        Route::get('/image-search-results', [MangaLotEstimationController::class, 'showSearchResults'])->name('en.image.search.results');
        Route::get('/lot-estimation-results', [MangaLotEstimationController::class, 'showSearchResults'])->name('en.lot.estimation.results');
        
        // Image analysis with GPT-4o (authentifiée)
        Route::get('/announcement-generator', [App\Http\Controllers\GenerateurAnnoncesController::class, 'index'])->name('en.generateur.annonces');
Route::post('/announcement-generator', [App\Http\Controllers\GenerateurAnnoncesController::class, 'generate'])->name('en.generateur.annonces.generate');
Route::get('/announcement-generator-results', [App\Http\Controllers\GenerateurAnnoncesController::class, 'showResults'])->name('en.generateur.annonces.results');
        
        // User profile
        Route::get('/my-profile', [ProfileController::class, 'edit'])->name('en.my.profile');
        Route::patch('/my-profile', [ProfileController::class, 'update'])->name('en.my.profile.update');
        Route::delete('/my-profile', [ProfileController::class, 'destroy'])->name('en.my.profile.destroy');
    });
    
    // Route publique pour afficher les images
    Route::get('/show-image/{filename}', [MangaLotEstimationController::class, 'show'])->name('en.show.image');
    
    // Redirects
    Route::get('/register', function () {
        return redirect()->route('login')->with('status', __('messages.registration_disabled'));
    })->name('en.register');
    
    Route::post('/register', function () {
        return redirect()->route('login')->with('status', __('messages.registration_disabled'));
    });
});

// Routes d'authentification (sans préfixe de langue)
require __DIR__.'/auth.php';
