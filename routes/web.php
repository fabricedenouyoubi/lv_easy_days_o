<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutorisationController;
use Illuminate\Support\Facades\Route;




Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('permissions')->name('permission.')->group(function () {
        Route::get('/', [AutorisationController::class, 'permission'])->name('index');
    });
        Route::prefix('utilisateurs')->name('gestion_utilisateur.')->group(function () {
        Route::get('/', [AutorisationController::class, 'gestion_utilisateur'])->name('index');
    });
});
