<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\ImageController;
use App\Helpers\LocalizedRoute;

// Routes d'authentification (sans préfixe de langue)
Route::get('/', function () {
    if (Auth::check()) {
        $locale = config('languages.default');
        return redirect('/' . $locale . '/price');
    } else {
        return redirect()->route('login');
    }
});

Route::get('/login', function () {
    return redirect()->route('login');
});

// Routes localisées avec préfixe de langue
$supportedLocales = implode('|', array_keys(config('languages.supported')));
Route::prefix('{locale}')->where(['locale' => $supportedLocales])->middleware('setlocale')->group(function () {
    
    // Route racine localisée
    Route::get('/', function () {
        if (Auth::check()) {
            return LocalizedRoute::redirectToLocalized('price.search');
        } else {
            return redirect()->route('login');
        }
    });
    
    // Routes principales
    Route::get('/dashboard', function () {
        return LocalizedRoute::redirectToLocalized('price.search');
    })->middleware(['auth', 'verified'])->name('dashboard');
    
    // Routes de prix
    Route::get('/price', [PriceController::class, 'index'])->name('price.search')->middleware('auth');
    Route::post('/price/search', [PriceController::class, 'search'])->name('price.search.submit')->middleware('auth');
    Route::post('/price/verify-isbn', [PriceController::class, 'verifyIsbn'])->name('price.verify.isbn')->middleware('auth');
    Route::get('/price/historique', [PriceController::class, 'historique'])->name('price.historique')->middleware('auth');
    
    // Routes pour afficher les fichiers HTML
    Route::get('/price/show/amazon/{id?}', [PriceController::class, 'showAmazon'])->name('price.show.amazon')->middleware('auth');
    Route::get('/price/show/cultura/{id?}', [PriceController::class, 'showCultura'])->name('price.show.cultura')->middleware('auth');
    Route::get('/price/show/fnac/{id?}', [PriceController::class, 'showFnac'])->name('price.show.fnac')->middleware('auth');
    
    // Routes pour le chargement d'image
    Route::get('/image', [ImageController::class, 'index'])->name('image.upload.form')->middleware('auth');
    Route::post('/image/upload', [ImageController::class, 'upload'])->name('image.upload.process')->middleware('auth');
    Route::post('/image/upload/ajax', [ImageController::class, 'uploadAjax'])->name('image.upload.ajax')->middleware('auth');
    Route::post('/image/search-isbn', [ImageController::class, 'searchIsbnByTitle'])->name('image.search.isbn')->middleware('auth');
    Route::post('/image/search-price', [ImageController::class, 'searchPrice'])->name('image.search.price')->middleware('auth');
    Route::post('/image/search-all-prices', [ImageController::class, 'searchAllPrices'])->name('image.search.all.prices')->middleware('auth');
    Route::post('/image/update-isbn', [ImageController::class, 'updateMangaIsbn'])->name('image.update.isbn')->middleware('auth');
    Route::post('/image/remove-manga', [ImageController::class, 'removeManga'])->name('image.remove.manga')->middleware('auth');
    Route::get('/image/show/{filename}', [ImageController::class, 'show'])->name('image.show');
    Route::get('/image/search-results', [ImageController::class, 'showSearchResults'])->name('image.search.results')->middleware('auth');
    
    // Routes de profil
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
    
    // Redirection pour les tentatives d'accès à l'enregistrement
    Route::get('/register', function () {
        return redirect()->route('login')->with('status', __('messages.registration_disabled'));
    })->name('register');
    
    Route::post('/register', function () {
        return redirect()->route('login')->with('status', __('messages.registration_disabled'));
    });
});

// Routes d'authentification (sans préfixe de langue)
require __DIR__.'/auth.php';
