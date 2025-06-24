<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutorisationController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

//--- Liens Utilisées par laravel breeze

/* Route::view('/welcome', 'welcome');

Route::view('dashboard', 'brezze-dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile'); */

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'index'])->name('dashboard')->middleware('verified');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('permissions')->name('permission.')->group(function () {
        Route::get('/', [AutorisationController::class, 'permission'])->name('index');
    });
    Route::prefix('groupes')->name('group.')->group(function () {
        Route::get('/', [AutorisationController::class, 'groupe'])->name('index');
    });
    Route::prefix('utilisateurs')->name('gestion_utilisateur.')->group(function () {
        Route::get('/', [AutorisationController::class, 'gestion_utilisateur'])->name('index');
    });
});
