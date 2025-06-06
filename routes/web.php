<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Routes temporaires pour éviter les erreurs
Route::get('/search', function() { return 'Recherche à venir'; })->name('search');
Route::get('/timesheet', function() { return 'Module Timesheet à venir'; })->name('timesheet.index');
Route::get('/timesheet/create', function() { return 'Création feuille à venir'; })->name('timesheet.create');
Route::get('/timesheet/pending', function() { return 'Feuilles en attente'; })->name('timesheet.pending');
Route::get('/timesheet/validation/pending', function() { return 'Validation en attente'; })->name('timesheet.validation.pending');
Route::get('/timesheet/validation/history', function() { return 'Historique validation'; })->name('timesheet.validation.history');
Route::get('/absence/request', function() { return 'Demande absence'; })->name('absence.request');
Route::get('/absence/my', function() { return 'Mes absences'; })->name('absence.my');
Route::get('/notifications', function() { return 'Notifications'; })->name('notifications.index');
Route::get('/profile', function() { return 'Profil'; })->name('profile.show');
Route::get('/help', function() { return 'Aide'; })->name('help');
Route::get('/settings', function() { return 'Paramètres'; })->name('settings');
Route::post('/logout', function() { return redirect('/'); })->name('logout');

// Routes admin temporaires
Route::get('/admin/employees', function() { return 'Liste employés'; })->name('admin.employees.index');
Route::get('/admin/employees/create', function() { return 'Ajouter employé'; })->name('admin.employees.create');
Route::get('/admin/config/work-codes', function() { return 'Codes de travail'; })->name('admin.config.work-codes');
Route::get('/admin/config/timesheet-settings', function() { return 'Paramètres feuilles'; })->name('admin.config.timesheet-settings');