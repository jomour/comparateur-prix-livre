<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PriceController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('price.search');
    } else {
        return redirect()->route('login');
    }
});

Route::get('/dashboard', function () {
    return redirect()->route('price.search');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/price', [PriceController::class, 'index'])->name('price.search')->middleware('auth');
Route::post('/price/search', [PriceController::class, 'search'])->name('price.search.submit')->middleware('auth');
Route::get('/price/historique', [PriceController::class, 'historique'])->name('price.historique')->middleware('auth');

// Routes pour afficher les fichiers HTML
Route::get('/price/show/amazon/{id?}', [PriceController::class, 'showAmazon'])->name('price.show.amazon')->middleware('auth');
Route::get('/price/show/cultura/{id?}', [PriceController::class, 'showCultura'])->name('price.show.cultura')->middleware('auth');
Route::get('/price/show/fnac/{id?}', [PriceController::class, 'showFnac'])->name('price.show.fnac')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Routes d'authentification désactivées pour l'enregistrement
// Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
// Route::post('/register', [RegisteredUserController::class, 'store']);

// Redirection pour les tentatives d'accès à l'enregistrement
Route::get('/register', function () {
    return redirect()->route('login')->with('status', 'L\'enregistrement de nouveaux comptes est désactivé. Veuillez contacter l\'administrateur.');
})->name('register');

Route::post('/register', function () {
    return redirect()->route('login')->with('status', 'L\'enregistrement de nouveaux comptes est désactivé. Veuillez contacter l\'administrateur.');
});
