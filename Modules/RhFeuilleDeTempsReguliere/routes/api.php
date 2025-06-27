<?php

use Illuminate\Support\Facades\Route;
use Modules\RhFeuilleDeTempsReguliere\Http\Controllers\RhFeuilleDeTempsReguliereController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rhfeuilledetempsregulieres', RhFeuilleDeTempsReguliereController::class)->names('rhfeuilledetempsreguliere');
});
