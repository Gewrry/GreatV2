<?php

use App\Http\Controllers\ProfileController;

//Admin Controllers
use App\Http\Livewire\Admin\AdminDashboard;
use App\Http\Livewire\Admin\AccountsManager;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\BarangayController;
use App\Http\Controllers\Admin\DatabaseBackupController;

//HR Controllers
use App\Http\Controllers\Hr\HumanResourcesController;

//RPT Controllers
use App\Http\Controllers\RPT\RPTController;

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

    // Use Livewire component for admin dashboard
    Route::get('/admin/dashboard', AdminDashboard::class)
        ->name('admin.dashboard.index');

    // Use Livewire component for accounts management
    Route::get('/accounts', AccountsManager::class)
        ->name('accounts.index');

    //DEPARTMENT MANAGEMENT
    Route::get('/departments', [DepartmentController::class, 'index'])->name('admin.departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('admin.departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('admin.departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('admin.departments.destroy');

    // BARANGAY MANAGEMENT
    Route::get('/barangays', [BarangayController::class, 'index'])->name('admin.barangays.index');
    Route::post('/barangays', [BarangayController::class, 'store'])->name('admin.barangays.store');
    Route::put('/barangays/{barangay}', [BarangayController::class, 'update'])->name('admin.barangays.update');
    Route::delete('/barangays/{barangay}', [BarangayController::class, 'destroy'])->name('admin.barangays.destroy');


    Route::get('/backup-database', [DatabaseBackupController::class, 'backup'])
        ->name('database.backup');
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
