<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HumanResourcesController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/employee-info/create', [HumanResourcesController::class, 'create'])->name('employee-info.create');
    Route::post('/employee-info', [HumanResourcesController::class, 'store'])->name('employee-info.store');
});
Route::middleware(['auth'])->group(function () {
    // Use Livewire component for accounts management
    Route::get('/accounts', \App\Http\Livewire\AccountsManager::class)->name('accounts.index');

    // Keep AJAX routes for any additional functionality
    Route::get('/accounts/{id}/details', [AdminController::class, 'show'])->name('accounts.show');
    Route::get('/accounts/check-username', [AdminController::class, 'checkUsername'])->name('accounts.checkUsername');
    Route::get('/accounts/check-employee', [AdminController::class, 'checkEmployee'])->name('accounts.checkEmployee');
});

require __DIR__ . '/auth.php';
