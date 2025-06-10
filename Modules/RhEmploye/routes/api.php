<?php

use Illuminate\Support\Facades\Route;
use Modules\RhEmploye\Http\Controllers\RhEmployeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rhemployes', RhEmployeController::class)->names('rhemploye');
});
