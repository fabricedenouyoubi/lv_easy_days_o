<?php

use Illuminate\Support\Facades\Route;
use Modules\Entreprise\Http\Controllers\EntrepriseController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('entreprises', EntrepriseController::class)->names('entreprise');
});
