<?php

use Illuminate\Support\Facades\Route;
use Modules\Rh\Http\Controllers\PosteController;
use Modules\Rh\Http\Controllers\RhController;

/* Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rhs', RhController::class)->names('rh');
}); */

Route::prefix('rh')->name('rh.poste.')->controller(PosteController::class)->group(function(){
    Route::get('/poste', 'index')->name('list');
});
