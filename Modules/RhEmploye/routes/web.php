<?php

use Illuminate\Support\Facades\Route;
use Modules\RhEmploye\Http\Controllers\RhEmployeController;

/* Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rhemployes', RhEmployeController::class)->names('rhemploye');
}); */

Route::prefix('rh/employe/')->name('rh-employe.')->middleware(['web', 'auth'])->group(function() {
    Route::get('', [RhEmployeController::class, 'index'])->name('list');
    Route::get('{employe}/detail', [RhEmployeController::class, 'show'])->name('show');
});
//
