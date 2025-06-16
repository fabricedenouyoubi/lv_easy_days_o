<?php

use Illuminate\Support\Facades\Route;
use Modules\RhCodeTravailComportement\Http\Controllers\RhCodeTravailComportementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rhcodetravailcomportements', RhCodeTravailComportementController::class)->names('rhcodetravailcomportement');
});
