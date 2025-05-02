<?php

use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\ContextualSearchController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\ModelSearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/global-search', [GlobalSearchController::class, 'index'])->name('global-search');
Route::get('/global-suggestions', [GlobalSearchController::class, 'suggestions'])->name('global-suggestions');

Route::get('/contextual-search', [ContextualSearchController::class, 'index'])->name('contextual-search');
Route::get('/contextual-suggestions', [ContextualSearchController::class, 'suggestions'])->name('contextual-suggestions');

Route::get('/user-search', [UserSearchController::class, 'index'])->name('user-search');
Route::post('/user-search', [UserSearchController::class, 'search'])->name('user-search.post');

Route::get('/user-suggestions', [UserSearchController::class, 'suggestions'])->name('user-suggestions');

Route::get('/model/{model}/search', [ModelSearchController::class, 'search'])->name('model-suggestions');

Route::get('/model/{model}/show/{id}', [\App\Http\Controllers\ModelController::class, 'show'])->name('model.show');

Route::middleware('auth')->group(function () {
    Route::get('/model/{model}', [\App\Http\Controllers\ModelController::class, 'index'])
        ->name('model.index');
    Route::get('/model/{model}/{action}/{id?}', [\App\Http\Controllers\ModelController::class, 'form'])
        ->name('model.form');
    Route::post('/model/{model}/{action}/{id?}', [\App\Http\Controllers\ModelController::class, 'submit'])
        ->name('model.submit');
});


require __DIR__ . '/auth.php';
