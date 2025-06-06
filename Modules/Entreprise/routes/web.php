<?php

use Illuminate\Support\Facades\Route;
use Modules\Entreprise\Http\Controllers\EntrepriseController;

Route::group(['prefix' => 'entreprise', 'as' => 'entreprise.'], function () {
    Route::get('/', [EntrepriseController::class, 'index'])->name('index');
    Route::get('/presentation', [EntrepriseController::class, 'presentation'])->name('presentation');
    Route::get('/sites', [EntrepriseController::class, 'sites'])->name('sites');
});
