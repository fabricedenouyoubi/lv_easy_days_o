<?php

use Illuminate\Support\Facades\Route;
use Modules\Budget\Http\Controllers\BudgetController;
use Modules\Budget\Http\Controllers\SemaineController;

Route::prefix('budget')->name('budget.')->middleware(['web', 'auth'])->group(function() {
    Route::get('/annees-financieres', [BudgetController::class, 'anneesFinancieres'])->name('annees-financieres');
    //Route::get('/annee/{annee}/details', [BudgetController::class, 'detailsAnnee'])->name('annee-details');
    //Route pour la liste des semaines d'une année financière
    Route::get('/annee/{annee}/details', [SemaineController::class, 'detailsAnnee'])->name('details-annee');
});
