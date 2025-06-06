<?php

use Illuminate\Support\Facades\Route;
use Modules\Budget\Http\Controllers\BudgetController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('budgets', BudgetController::class)->names('budget');
});
