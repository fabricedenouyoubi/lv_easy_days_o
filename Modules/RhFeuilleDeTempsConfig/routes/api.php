<?php

use Illuminate\Support\Facades\Route;
use Modules\RhFeuilleDeTempsConfig\Http\Controllers\RhFeuilleDeTempsConfigController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rhfeuilledetempsconfigs', RhFeuilleDeTempsConfigController::class)->names('rhfeuilledetempsconfig');
});
