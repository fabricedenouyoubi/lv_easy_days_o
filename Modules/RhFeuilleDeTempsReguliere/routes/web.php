<?php

use Illuminate\Support\Facades\Route;
use Modules\RhFeuilleDeTempsReguliere\Http\Controllers\RhFeuilleDeTempsReguliereController;

// Routes pour les feuilles de temps régulières
Route::prefix('rh-feuille-de-temps-reguliere/feuille-temps')->name('feuille-temps.')->controller(RhFeuilleDeTempsReguliereController::class)->middleware(['auth', 'verified'])->group(function () {
    
    // Liste des feuilles de temps pour l'employé connecté
    Route::get('', 'index')->name('list');
    // Créer/éditer une feuille de temps
    Route::get('/{semaineId}/edit', 'edit')->name('edit');
    Route::get('/{semaineId}/edit/{operationId}', 'edit')->name('edit');
    
    // Consulter une feuille de temps
    Route::get('/{semaineId}/show/{operationId}', 'show')->name('show');
    
    // Gestionnaire - tableau de bord
    Route::get('/dashboard', 'managerDashboard')->name('dashboard')->middleware('permission:Gestion Feuilles Temps');
});


