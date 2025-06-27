<?php

use Illuminate\Support\Facades\Route;
use Modules\RhFeuilleDeTempsReguliere\Http\Controllers\RhFeuilleDeTempsReguliereController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rhfeuilledetempsregulieres', RhFeuilleDeTempsReguliereController::class)->names('rhfeuilledetempsreguliere');
});
