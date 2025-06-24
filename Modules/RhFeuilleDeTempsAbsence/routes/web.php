<?php

use Illuminate\Support\Facades\Route;
use Modules\RhFeuilleDeTempsAbsence\Http\Controllers\RhFeuilleDeTempsAbsenceController;

/* Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rhfeuilledetempsabsences', RhFeuilleDeTempsAbsenceController::class)->names('rhfeuilledetempsabsence');
}); */

Route::prefix('rh-feuille-de-temps-absence/demande/absence')->name('absence.')->controller(RhFeuilleDeTempsAbsenceController::class)->middleware(['auth', 'verified'])->group(function () {
    Route::get('', 'index')->name('list');
});
