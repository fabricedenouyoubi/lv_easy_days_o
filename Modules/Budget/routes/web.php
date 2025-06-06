<?php

use Illuminate\Support\Facades\Route;
use Modules\Budget\Http\Controllers\BudgetController;

Route::prefix('budget')->name('budget.')->group(function() {
    Route::get('/', [BudgetController::class, 'index'])->name('index');
    Route::get('/annees-financieres', [BudgetController::class, 'anneesFinancieres'])->name('annees-financieres');
});