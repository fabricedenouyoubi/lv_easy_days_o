<?php

use Illuminate\Support\Facades\Route;
use Modules\RhFeuilleDeTempsConfig\Http\Controllers\RhFeuilleDeTempsConfigController;

/* Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rhfeuilledetempsconfigs', RhFeuilleDeTempsConfigController::class)->names('rhfeuilledetempsconfig');
}); */

Route::prefix('rh-feuille-de-temps-config')->name('rhfeuilledetempsconfig.')->middleware('web')->group(function() {
    //Route::get('/', [RhFeuilleDeTempsConfigController::class, 'index'])->name('index');
    Route::get('/annee/{annee}/details', [RhFeuilleDeTempsConfigController::class, 'detailsAnnee'])->name('details-annee');
});
