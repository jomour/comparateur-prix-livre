<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SitemapController;
use App\Helpers\LocalizedRoute;

// Routes d'authentification (sans préfixe de langue)
Route::get('/', function () {
    if (Auth::check()) {
        $locale = config('languages.default');
        return redirect('/' . $locale . '/' . ($locale === 'fr' ? 'comparateur-prix-manga' : 'manga-price-comparator'));
    } else {
        return redirect()->route('login');
    }
});

Route::get('/login', function () {
    return redirect()->route('login');
});

// Routes SEO
Route::get('/sitemap.xml', [SitemapController::class, 'generate'])->name('sitemap.xml');
Route::get('/sitemap-index.xml', [SitemapController::class, 'index'])->name('sitemap.index');

// Routes françaises optimisées SEO
Route::prefix('fr')->middleware('setlocale')->group(function () {
    
    // Route racine française
    Route::get('/', function () {
        if (Auth::check()) {
            return redirect()->route('fr.comparateur.prix');
        } else {
            return redirect()->route('login');
        }
    });
    
    // Routes principales françaises avec URLs SEO
    Route::get('/dashboard', function () {
        return redirect()->route('fr.comparateur.prix');
    })->middleware(['auth', 'verified'])->name('fr.dashboard');
    
    // Comparateur de prix manga - URLs SEO françaises
    Route::get('/comparateur-prix-manga', [PriceController::class, 'index'])->name('fr.comparateur.prix')->middleware('auth');
    Route::get('/prix-manga', [PriceController::class, 'index'])->name('fr.prix.manga')->middleware('auth');
    Route::get('/comparateur-prix-livres', [PriceController::class, 'index'])->name('fr.comparateur.livres')->middleware('auth');
    Route::get('/economiser-manga', [PriceController::class, 'index'])->name('fr.economiser.manga')->middleware('auth');
    Route::get('/meilleur-prix-manga', [PriceController::class, 'index'])->name('fr.meilleur.prix')->middleware('auth');
    
    // Actions de recherche
    Route::post('/comparateur-prix-manga/recherche', [PriceController::class, 'search'])->name('fr.comparateur.recherche')->middleware('auth');
    Route::post('/prix-manga/recherche', [PriceController::class, 'search'])->name('fr.prix.recherche')->middleware('auth');
    Route::post('/verifier-isbn', [PriceController::class, 'verifyIsbn'])->name('fr.verifier.isbn')->middleware('auth');
    
    // Historique des recherches
    Route::get('/historique-recherches', [HistoriqueController::class, 'index'])->name('fr.historique.recherches')->middleware('auth');
    Route::get('/historique-prix', [HistoriqueController::class, 'index'])->name('fr.historique.prix')->middleware('auth');
    Route::get('/mes-recherches', [HistoriqueController::class, 'index'])->name('fr.mes.recherches')->middleware('auth');
    Route::get('/historique-recherches/{id}', [HistoriqueController::class, 'show'])->name('fr.historique.show')->middleware('auth');
    

    
    // Recherche par image
    Route::get('/estimation-lot-manga', [ImageController::class, 'index'])->name('fr.estimation.lot.manga')->middleware('auth');
    Route::get('/recherche-photo', [ImageController::class, 'index'])->name('fr.recherche.photo')->middleware('auth');
    Route::post('/upload-image', [ImageController::class, 'upload'])->name('fr.upload.image')->middleware('auth');
    Route::post('/upload-image-ajax', [ImageController::class, 'uploadAjax'])->name('fr.upload.image.ajax')->middleware('auth');
    Route::post('/recherche-isbn-image', [ImageController::class, 'searchIsbnByTitle'])->name('fr.recherche.isbn.image')->middleware('auth');
    Route::post('/recherche-prix-image', [ImageController::class, 'searchPrice'])->name('fr.recherche.prix.image')->middleware('auth');
    Route::post('/recherche-tous-prix', [ImageController::class, 'searchAllPrices'])->name('fr.recherche.tous.prix')->middleware('auth');
    Route::post('/mettre-a-jour-isbn', [ImageController::class, 'updateMangaIsbn'])->name('fr.mettre.a.jour.isbn')->middleware('auth');
    Route::post('/supprimer-manga', [ImageController::class, 'removeManga'])->name('fr.supprimer.manga')->middleware('auth');
    Route::get('/afficher-image/{filename}', [ImageController::class, 'show'])->name('fr.afficher.image');
    Route::get('/resultats-recherche-image', [ImageController::class, 'showSearchResults'])->name('fr.resultats.recherche.image')->middleware('auth');
    
    // Profil utilisateur
    Route::middleware('auth')->group(function () {
        Route::get('/mon-profil', [ProfileController::class, 'edit'])->name('fr.mon.profil');
        Route::patch('/mon-profil', [ProfileController::class, 'update'])->name('fr.mon.profil.update');
        Route::delete('/mon-profil', [ProfileController::class, 'destroy'])->name('fr.mon.profil.destroy');
    });
    
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
    Route::get('/', function () {
        if (Auth::check()) {
            return redirect()->route('en.manga.price.comparator');
        } else {
            return redirect()->route('login');
        }
    });
    
    // Routes principales anglaises avec URLs SEO
    Route::get('/dashboard', function () {
        return redirect()->route('en.manga.price.comparator');
    })->middleware(['auth', 'verified'])->name('en.dashboard');
    
    // Manga price comparator - URLs SEO anglaises
    Route::get('/manga-price-comparator', [PriceController::class, 'index'])->name('en.manga.price.comparator')->middleware('auth');
    Route::get('/manga-prices', [PriceController::class, 'index'])->name('en.manga.prices')->middleware('auth');
    Route::get('/manga-book-price-comparison', [PriceController::class, 'index'])->name('en.manga.book.comparison')->middleware('auth');
    Route::get('/save-money-manga', [PriceController::class, 'index'])->name('en.save.money.manga')->middleware('auth');
    Route::get('/best-manga-price', [PriceController::class, 'index'])->name('en.best.manga.price')->middleware('auth');
    Route::get('/manga-price-checker', [PriceController::class, 'index'])->name('en.manga.price.checker')->middleware('auth');
    
    // Search actions
    Route::post('/manga-price-comparator/search', [PriceController::class, 'search'])->name('en.manga.price.search')->middleware('auth');
    Route::post('/manga-prices/search', [PriceController::class, 'search'])->name('en.manga.prices.search')->middleware('auth');
    Route::post('/verify-isbn', [PriceController::class, 'verifyIsbn'])->name('en.verify.isbn')->middleware('auth');
    
    // Search history
    Route::get('/search-history', [HistoriqueController::class, 'index'])->name('en.search.history')->middleware('auth');
    Route::get('/price-history', [HistoriqueController::class, 'index'])->name('en.price.history')->middleware('auth');
    Route::get('/my-searches', [HistoriqueController::class, 'index'])->name('en.my.searches')->middleware('auth');
    Route::get('/search-history/{id}', [HistoriqueController::class, 'show'])->name('en.historique.show')->middleware('auth');
    

    
    // Image search
    Route::get('/manga-lot-estimation', [ImageController::class, 'index'])->name('en.manga.lot.estimation')->middleware('auth');
    Route::get('/photo-search', [ImageController::class, 'index'])->name('en.photo.search')->middleware('auth');
    Route::post('/upload-image', [ImageController::class, 'upload'])->name('en.upload.image')->middleware('auth');
    Route::post('/upload-image-ajax', [ImageController::class, 'uploadAjax'])->name('en.upload.image.ajax')->middleware('auth');
    Route::post('/search-isbn-image', [ImageController::class, 'searchIsbnByTitle'])->name('en.search.isbn.image')->middleware('auth');
    Route::post('/search-price-image', [ImageController::class, 'searchPrice'])->name('en.search.price.image')->middleware('auth');
    Route::post('/search-all-prices', [ImageController::class, 'searchAllPrices'])->name('en.search.all.prices')->middleware('auth');
    Route::post('/update-isbn', [ImageController::class, 'updateMangaIsbn'])->name('en.update.isbn')->middleware('auth');
    Route::post('/remove-manga', [ImageController::class, 'removeManga'])->name('en.remove.manga')->middleware('auth');
    Route::get('/show-image/{filename}', [ImageController::class, 'show'])->name('en.show.image');
    Route::get('/image-search-results', [ImageController::class, 'showSearchResults'])->name('en.image.search.results')->middleware('auth');
    
    // User profile
    Route::middleware('auth')->group(function () {
        Route::get('/my-profile', [ProfileController::class, 'edit'])->name('en.my.profile');
        Route::patch('/my-profile', [ProfileController::class, 'update'])->name('en.my.profile.update');
        Route::delete('/my-profile', [ProfileController::class, 'destroy'])->name('en.my.profile.destroy');
    });
    
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
