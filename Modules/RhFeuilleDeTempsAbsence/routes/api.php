<?php

use Illuminate\Support\Facades\Route;
use Modules\RhFeuilleDeTempsAbsence\Http\Controllers\RhFeuilleDeTempsAbsenceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rhfeuilledetempsabsences', RhFeuilleDeTempsAbsenceController::class)->names('rhfeuilledetempsabsence');
});
