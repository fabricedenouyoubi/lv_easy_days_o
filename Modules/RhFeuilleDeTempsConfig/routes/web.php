<?php

use Illuminate\Support\Facades\Route;
use Modules\RhFeuilleDeTempsConfig\Http\Controllers\CategorieController;
use Modules\RhFeuilleDeTempsConfig\Http\Controllers\CodeTravailController;
use Modules\RhFeuilleDeTempsConfig\Http\Controllers\ComportementController;
use Modules\RhFeuilleDeTempsConfig\Http\Controllers\RhFeuilleDeTempsConfigController;


Route::prefix('rh-feuille-de-temps-config')->name('rhfeuilledetempsconfig.')->middleware(['web', 'auth'])->group(function() {
    //Route pour la liste des semaines d'une année financière
    Route::get('/annee/{annee}/details', [RhFeuilleDeTempsConfigController::class, 'detailsAnnee'])->name('details-annee');

    // Routes pour les catégories
    Route::get('/categories', [CategorieController::class, 'categories'])->name('categories.categories');
    // Routes pour les codes de travail
    Route::get('/codes-travail', [CodeTravailController::class, 'codetravails'])->name('codes-travail.codetravails');
    // Route pour la configuration d'un code de travail
    Route::get('configure/{codeTravailId}', [ComportementController::class, 'configure'])->name('configure');
});

