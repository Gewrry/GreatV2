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
use App\Http\Controllers\RPT\RPTA_SETTINGS\RPTA_SettingsController;
use App\Http\Controllers\RPT\RPTA_SETTINGS\RptAuController;
use App\Http\Controllers\RPT\RPTA_SETTINGS\AdditionalItemController;    
use App\Http\Controllers\RPT\RPTA_SETTINGS\AssessmentLevelController;
use App\Http\Controllers\RPT\RPTA_SETTINGS\ClassificationController;

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

    //RPTA Settings
    Route::get('/rpta_settings', [RPTA_SettingsController::class, 'index'])->name('rpt.rpta_settings.index');
    Route::get('/rpta_settings/actual_use', [RPTA_SettingsController::class, 'actual_use'])->name('rpt.rpta_settings.actual_use');
    
    
    // Actual Use Routes
    Route::get('/actual-use', [RptAuController::class, 'index'])->name('rpt.actual-use.index');
    Route::post('/actual-use', [RptAuController::class, 'store'])->name('rpt.actual-use.store');
    Route::get('/actual-use/{rptAu}', [RptAuController::class, 'show'])->name('rpt.actual-use.show');
    Route::post('/actual-use/{rptAu}', [RptAuController::class, 'update'])->name('rpt.actual-use.update');
    Route::delete('/actual-use/{rptAu}', [RptAuController::class, 'destroy'])->name('rpt.actual-use.destroy');


    // Additional Item Routes
    Route::get('/additional-items', [AdditionalItemController::class, 'index'])->name('rpt.additional-items.index');
    Route::post('/additional-items', [AdditionalItemController::class, 'store'])->name('rpt.additional-items.store');
    Route::get('/additional-items/{additionalItem}', [AdditionalItemController::class, 'show'])->name('rpt.additional-items.show');
    Route::put('/additional-items/{additionalItem}', [AdditionalItemController::class, 'update'])->name('rpt.additional-items.update');
    Route::delete('/additional-items/{additionalItem}', [AdditionalItemController::class, 'destroy'])->name('rpt.additional-items.destroy');

    // Assessment Level Routes
    Route::get('/assessment-levels', [AssessmentLevelController::class, 'index'])->name('rpt.assessment-levels.index');
    Route::post('/assessment-levels', [AssessmentLevelController::class, 'store'])->name('rpt.assessment-levels.store');
    Route::put('/assessment-levels/{assessmentLevel}', [AssessmentLevelController::class, 'update'])->name('rpt.assessment-levels.update');
    Route::delete('/assessment-levels/{assessmentLevel}', [AssessmentLevelController::class, 'destroy'])->name('rpt.assessment-levels.destroy');
    Route::get('/assessment-levels/{assessmentLevel}', [AssessmentLevelController::class, 'show'])->name('rpt.assessment-levels.show');

    // Classification Routes
    Route::get('/classifications', [ClassificationController::class, 'index'])->name('rpt.classifications.index');
    Route::post('/classifications', [ClassificationController::class, 'store'])->name('rpt.classifications.store');
    Route::get('/classifications/{classification}', [ClassificationController::class, 'show'])->name('rpt.classifications.show');
    Route::put('/classifications/{classification}', [ClassificationController::class, 'update'])->name('rpt.classifications.update');
    Route::delete('/classifications/{classification}', [ClassificationController::class, 'destroy'])->name('rpt.classifications.destroy');
    });




require __DIR__ . '/auth.php';
