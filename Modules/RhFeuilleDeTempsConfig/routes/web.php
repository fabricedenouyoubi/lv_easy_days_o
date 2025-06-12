<?php

use Illuminate\Support\Facades\Route;
use Modules\RhFeuilleDeTempsConfig\Http\Controllers\CategorieController;
use Modules\RhFeuilleDeTempsConfig\Http\Controllers\CodeTravailController;
use Modules\RhFeuilleDeTempsConfig\Http\Controllers\RhFeuilleDeTempsConfigController;


Route::prefix('rh-feuille-de-temps-config')->name('rhfeuilledetempsconfig.')->middleware(['web', 'auth'])->group(function() {
    //Route pour la liste des semaines d'une année financière
    Route::get('/annee/{annee}/details', [RhFeuilleDeTempsConfigController::class, 'detailsAnnee'])->name('details-annee');

    // Routes pour les catégories
    Route::get('/categories', [CategorieController::class, 'categories'])->name('categories.categories');
    // Routes pour les codes de travail
    Route::get('/codes-travail', [CodeTravailController::class, 'codetravails'])->name('codes-travail.codetravails');
    Route::get('codes-travail/{id}/configure', [CodeTravailController::class, 'configure'])->name('codes-travail.configure');
});

