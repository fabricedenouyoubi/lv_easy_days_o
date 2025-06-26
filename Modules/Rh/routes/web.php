<?php

use Illuminate\Support\Facades\Route;
use Modules\Rh\Http\Controllers\RhController;

/* Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rhs', RhController::class)->names('rh');
}); */


Route::prefix('rh/employe/')->name('rh-employe.')->middleware(['web', 'auth'])->group(function() {
    Route::get('', [RhController::class, 'employe_list'])->name('list')->middleware(['permission:Voir Employé', 'permission:Voir Module RH']);
    Route::get('{employe}/detail', [RhController::class, 'employe_details'])->name('show')->middleware('permission:Voir Detail Employé');
});

