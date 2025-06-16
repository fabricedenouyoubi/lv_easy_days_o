<?php

use Illuminate\Support\Facades\Route;
use Modules\RhCodeTravailComportement\Http\Controllers\RhCodeTravailComportementController;

Route::prefix('rhcodetravailcomportement')->
name('rhcodetravailcomportement.')->middleware('web')->group(function() {
    
    // Route pour la configuration d'un code de travail
    Route::get('configure/{codeTravailId}', [RhCodeTravailComportementController::class, 'configure'])->name('configure');
    
});