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
use App\Http\Controllers\RPT\RPTA_SETTINGS\DepreciationRateBldgController;
use App\Http\Controllers\RPT\RPTA_SETTINGS\OwnerController;
use App\Http\Controllers\RPT\RPTA_SETTINGS\OtherImprovementController;
use App\Http\Controllers\RPT\RPTA_SETTINGS\RptaSignatoryController;
use App\Http\Controllers\RPT\RPTA_SETTINGS\RptTransactionCodeController;
use App\Http\Controllers\RPT\RPTA_SETTINGS\RptaGenRevController;


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

    // Depreciation Rate for Building Routes
    Route::get('/depreciation-rates', [DepreciationRateBldgController::class, 'index'])->name('rpt.depreciation-rates.index');
    Route::post('/depreciation-rates', [DepreciationRateBldgController::class, 'store'])->name('rpt.depreciation-rates.store');
    Route::get('/depreciation-rates/{depreciationRate}', [DepreciationRateBldgController::class, 'show'])->name('rpt.depreciation-rates.show');
    Route::put('/depreciation-rates/{depreciationRate}', [DepreciationRateBldgController::class, 'update'])->name('rpt.depreciation-rates.update');
    Route::delete('/depreciation-rates/{depreciationRate}', [DepreciationRateBldgController::class, 'destroy'])->name('rpt.depreciation-rates.destroy');

    // Owner Selection Routes
    Route::get('/owners', [OwnerController::class, 'index'])->name('rpt.owners.index');
    Route::post('/owners', [OwnerController::class, 'store'])->name('rpt.owners.store');
    Route::get('/owners/{owner}', [OwnerController::class, 'show'])->name('rpt.owners.show');
    Route::put('/owners/{owner}', [OwnerController::class, 'update'])->name('rpt.owners.update');
    Route::delete('/owners/{owner}', [OwnerController::class, 'destroy'])->name('rpt.owners.destroy');

    // Other Improvement Routes
    Route::get('/other-improvements', [OtherImprovementController::class, 'index'])->name('rpt.other-improvements.index');
    Route::post('/other-improvements', [OtherImprovementController::class, 'store'])->name('rpt.other-improvements.store');
    Route::get('/other-improvements/{improvement}', [OtherImprovementController::class, 'show'])->name('rpt.other-improvements.show');
    Route::put('/other-improvements/{improvement}', [OtherImprovementController::class, 'update'])->name('rpt.other-improvements.update');
    Route::delete('/other-improvements/{improvement}', [OtherImprovementController::class, 'destroy'])->name('rpt.other-improvements.destroy');

    // RPTA Signatories Routes
    Route::get('/signatories', [RptaSignatoryController::class, 'index'])->name('rpt.signatories.index');
    Route::post('/signatories', [RptaSignatoryController::class, 'store'])->name('rpt.signatories.store');
    Route::get('/signatories/{signatory}', [RptaSignatoryController::class, 'show'])->name('rpt.signatories.show');
    Route::put('/signatories/{signatory}', [RptaSignatoryController::class, 'update'])->name('rpt.signatories.update');
    Route::delete('/signatories/{signatory}', [RptaSignatoryController::class, 'destroy'])->name('rpt.signatories.destroy');
    Route::post('/signatories/update-revision-year', [RptaSignatoryController::class, 'updateRevisionYear'])->name('rpt.signatories.update-revision-year');
    Route::post('/signatories/update-rc-signatory', [RptaSignatoryController::class, 'updateRcSignatory'])->name('rpt.signatories.update-rc-signatory');

    // Transaction Code Routes
    Route::get('/transaction-codes', [RptTransactionCodeController::class, 'index'])->name('rpt.transaction_code.index');
    Route::post('/transaction-codes', [RptTransactionCodeController::class, 'store'])->name('rpt.transaction_code.store');
    Route::get('/transaction-codes/{transaction_code}', [RptTransactionCodeController::class, 'show'])->name('rpt.transaction_code.show');
    Route::put('/transaction-codes/{transaction_code}', [RptTransactionCodeController::class, 'update'])->name('rpt.transaction_code.update');
    Route::delete('/transaction-codes/{transaction_code}', [RptTransactionCodeController::class, 'destroy'])->name('rpt.transaction_code.destroy');

    // General Revision Routes
    Route::get('/general-revision', [RptaGenRevController::class, 'index'])->name('rpt.general_revision.index');
    Route::get('/general-revision/years', [RptaGenRevController::class, 'getMaxRevisionYear'])->name('rpt.general_revision.years');
    Route::post('/general-revision/process', [RptaGenRevController::class, 'processRevision'])->name('rpt.general_revision.process');
    Route::get('/general-revision/list', [RptaGenRevController::class, 'getRevisions'])->name('rpt.general_revision.list');
    Route::delete('/general-revision/{id}/cancel', [RptaGenRevController::class, 'cancelRevision'])->name('rpt.general_revision.cancel');
});




require __DIR__ . '/auth.php';
