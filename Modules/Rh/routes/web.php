<?php

use Illuminate\Support\Facades\Route;
use Modules\Rh\Http\Controllers\RhController;

/* Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rhs', RhController::class)->names('rh');
}); */


Route::prefix('rh/employe/')->name('rh-employe.')->middleware(['web', 'auth'])->group(function() {
    Route::get('', [RhController::class, 'employe_list'])->name('list');
    Route::get('{employe}/detail', [RhController::class, 'employe_details'])->name('show');
});

