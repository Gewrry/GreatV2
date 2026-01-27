<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Hr\HumanResourcesController;
use App\Http\Controllers\RPT\RPTController;
use App\Http\Livewire\Admin\AccountsManager;

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
    //HR
Route::middleware('auth')->group(function () {
    Route::get('/employee-info/create', [HumanResourcesController::class, 'create'])->name('employee-info.create');
    Route::post('/employee-info', [HumanResourcesController::class, 'store'])->name('employee-info.store');
});
//ADMIN - ACCOUNTS MANAGEMENT
Route::middleware(['auth'])->group(function () {
    // Use Livewire component for accounts management
    Route::get('/accounts', AccountsManager::class)
        ->name('accounts.index');
    // Keep AJAX routes for any additional functionality
    Route::get('/accounts/{id}/details', [AdminController::class, 'show'])->name('accounts.show');
    Route::get('/accounts/check-username', [AdminController::class, 'checkUsername'])->name('accounts.checkUsername');
    Route::get('/accounts/check-employee', [AdminController::class, 'checkEmployee'])->name('accounts.checkEmployee');
});

Route::middleware('auth')->group(function () {
    //Dashboard RPT
    Route::get('/rpt', [RPTController::class, 'index'])->name(name: 'rpt.index');

    //Faas List RPT
    Route::get('/rpt/faas_list', [RPTController::class, 'faas_list'])->name(name: 'rpt.faas_list');

    //Fast Entry
    Route::get('/rpt/land', [RPTController::class, 'land'])->name('rpt.faas_entry.land');
    Route::get('/rpt/building', [RPTController::class, 'building'])->name('rpt.faas_entry.building');
    Route::get('/rpt/machine', [RPTController::class, 'machine'])->name(name: 'rpt.faas_entry.machine');
    Route::get('/rpt/taxdec_based', [RPTController::class, 'taxdec_based'])->name(name: 'rpt.faas_entry.taxdec_based');


    Route::post('/rpt', [RPTController::class, 'store'])->name('rpt.store');
});

require __DIR__ . '/auth.php';
