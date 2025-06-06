<?php

use Illuminate\Support\Facades\Route;
use Modules\Budget\Http\Controllers\BudgetController;

Route::get('budgets', [BudgetController::class, 'index'])->name('budget.index');
Route::get('budgets/create', [BudgetController::class, 'create'])->name('budget.create');
Route::post('budgets', [BudgetController::class, 'store'])->name('budget.store');
Route::get('budgets/{id}/edit', [BudgetController::class, 'edit'])->name('budget.edit');
Route::put('budgets/{id}', [BudgetController::class, 'update'])->name('budget.update');
Route::delete('budgets/{id}', [BudgetController::class, 'destroy'])->name('budget.destroy');

Route::resource('budgets', BudgetController::class)->names('budget');