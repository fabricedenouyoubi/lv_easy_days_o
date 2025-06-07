<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');



// Routes entreprise
Route::get('/entreprise/presentation', function() { 
    return view('entreprise.presentation'); 
})->name('entreprise.presentation');

Route::get('/entreprise/sites', function() { 
    return view('entreprise.sites'); 
})->name('entreprise.sites');