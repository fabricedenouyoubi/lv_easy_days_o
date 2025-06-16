<?php

use Illuminate\Support\Facades\Route;
use Modules\Rh\Http\Controllers\RhController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rhs', RhController::class)->names('rh');
});
