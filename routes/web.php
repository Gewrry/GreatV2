<?php
// routes/web.php — COMPLETE FILE

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Livewire\Admin\AdminDashboard;
use App\Http\Livewire\Admin\AccountsManager;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\BarangayController;
use App\Http\Controllers\Admin\DatabaseBackupController;
use App\Http\Controllers\Hr\HumanResourcesController;
use App\Http\Controllers\RPT\RPTController;
use App\Http\Controllers\RPT\TaxDeclarationController;
use App\Http\Controllers\RPT\ReportController;
use App\Http\Controllers\RPT\GISController;
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
use App\Http\Controllers\BplsController;
use App\Http\Controllers\BplsPaymentController;
use App\Http\Controllers\BusinessEntriesController;
use App\Http\Controllers\BusinessListController;
use App\Http\Controllers\Bpls\FeeRuleController;


use App\Http\Controllers\BPLS\SettingsController;
use App\Http\Controllers\Bpls\MasterlistController;

Route::prefix('bpls/reports')->name('bpls.reports.')->middleware(['auth', 'verified'])->group(function () {

    // Business Masterlist
    Route::get('/masterlist', [MasterlistController::class, 'index'])->name('masterlist.index'); // GET  /bpls/reports/masterlist
    Route::get('/masterlist/data', [MasterlistController::class, 'data'])->name('masterlist.data');  // GET  /bpls/reports/masterlist/data

});

Route::prefix('bpls/settings')->name('bpls.settings.')->middleware(['auth', 'verified'])->group(function () {

    // Main settings page
    Route::get('/', [SettingsController::class, 'index'])->name('index');

    // OR Assignment API (JSON)
    Route::get('/or-assignments', [SettingsController::class, 'listOrAssignments'])->name('or-assignments.index');
    Route::post('/or-assignments', [SettingsController::class, 'storeOrAssignment'])->name('or-assignments.store');
    Route::put('/or-assignments/{orAssignment}', [SettingsController::class, 'updateOrAssignment'])->name('or-assignments.update');
    Route::delete('/or-assignments/{orAssignment}', [SettingsController::class, 'destroyOrAssignment'])->name('or-assignments.destroy');

});



Route::get('/', fn() => view('welcome'));
Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

// BPLS public search
Route::prefix('bpls')->name('bpls.')->group(function () {
    Route::get('/search-owner', [BusinessEntriesController::class, 'searchOwner'])->name('search-owner');
    Route::get('/search-business', [BusinessEntriesController::class, 'searchBusiness'])->name('search-business');


});

// BPLS protected
Route::middleware('auth')->prefix('bpls')->name('bpls.')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/update-general', [SettingsController::class, 'updateGeneral'])->name('settings.update-general');
    Route::post('/settings/update-discount', [SettingsController::class, 'updateDiscount'])->name('settings.update-discount');
    Route::post('/settings/update-permit', [SettingsController::class, 'updatePermit'])->name('settings.update-permit');
    Route::get('/', [BplsController::class, 'index'])->name('index');
    Route::post('/payment/{entry}/compute-surcharge', [BplsPaymentController::class, 'computeSurcharge'])->name('payment.compute-surcharge');
    Route::get('/payment/{entry}/permit/{payment}', [BplsPaymentController::class, 'permit'])->name('payment.permit');
    // Permit before broad payment wildcard
    Route::get('/payment/{entry}/permit/{payment}', [BplsPaymentController::class, 'permit'])->name('payment.permit');

    // Business Entries
    Route::get('/business-entries', [BusinessEntriesController::class, 'index'])->name('business-entries.index');
    Route::post('/business-entries', [BusinessEntriesController::class, 'store'])->name('business-entries.store');

    // Business List — static routes BEFORE /{entry} wildcard
    Route::get('/business-list', [BusinessListController::class, 'index'])->name('business-list.index');
    Route::get('/business-list/search', [BusinessListController::class, 'search'])->name('business-list.search');
    Route::get('/business-list/{entry}', [BusinessListController::class, 'show'])->name('business-list.show');
    Route::post('/business-list/{entry}/assess', [BusinessListController::class, 'assess'])->name('business-list.assess');
    Route::post('/business-list/{entry}/approve-payment', [BplsPaymentController::class, 'approvePayment'])->name('business-list.approve-payment');
    Route::post('/business-list/{entry}/approve-renewal', [BusinessListController::class, 'approveRenewal'])->name('business-list.approve-renewal');
    Route::post('/business-list/{entry}/change-status', [BusinessListController::class, 'changeStatus'])->name('business-list.change-status');
    Route::post('/business-list/{entry}/retire', [BusinessListController::class, 'retire'])->name('business-list.retire');
    Route::get('/business-list/{entry}/retirement-certificate', [BusinessListController::class, 'retirementCertificate'])->name('business-list.retirement-certificate');

    // Payment 51C
    Route::get('/payment/{entry}', [BplsPaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{entry}/pay', [BplsPaymentController::class, 'pay'])->name('payment.pay');
    Route::get('/payment/{entry}/receipt/{payment}', [BplsPaymentController::class, 'receipt'])->name('payment.receipt');
    Route::post('/payment/{entry}/compute-surcharge', [BplsPaymentController::class, 'computeSurcharge'])->name('payment.compute-surcharge');

    // Fee Rules — static before /{feeRule} wildcard
    Route::get('/fee-rules/manage', fn() => view('modules.bpls.fee-rules.index'))->name('fee-rules.manage');
    Route::post('/fee-rules/reorder', [FeeRuleController::class, 'reorder'])->name('fee-rules.reorder');
    Route::post('/fee-rules/reset-defaults', [FeeRuleController::class, 'resetDefaults'])->name('fee-rules.reset-defaults');
    Route::post('/fee-rules/compute', [FeeRuleController::class, 'compute'])->name('fee-rules.compute');
    Route::get('/fee-rules', [FeeRuleController::class, 'index'])->name('fee-rules.index');
    Route::post('/fee-rules', [FeeRuleController::class, 'store'])->name('fee-rules.store');
    Route::get('/fee-rules/{feeRule}', [FeeRuleController::class, 'show'])->name('fee-rules.show');
    Route::put('/fee-rules/{feeRule}', [FeeRuleController::class, 'update'])->name('fee-rules.update');
    Route::delete('/fee-rules/{feeRule}', [FeeRuleController::class, 'destroy'])->name('fee-rules.destroy');
    Route::post('/fee-rules/{feeRule}/toggle', [FeeRuleController::class, 'toggle'])->name('fee-rules.toggle');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// HR
Route::middleware('auth')->group(function () {
    Route::get('/employee-info/create', [HumanResourcesController::class, 'create'])->name('employee-info.create');
    Route::post('/employee-info', [HumanResourcesController::class, 'store'])->name('employee-info.store');
});

// Admin
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard.index');
    Route::get('/accounts', AccountsManager::class)->name('accounts.index');

    Route::get('/departments', [DepartmentController::class, 'index'])->name('admin.departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('admin.departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('admin.departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('admin.departments.destroy');

    Route::get('/barangays', [BarangayController::class, 'index'])->name('admin.barangays.index');
    Route::post('/barangays', [BarangayController::class, 'store'])->name('admin.barangays.store');
    Route::put('/barangays/{barangay}', [BarangayController::class, 'update'])->name('admin.barangays.update');
    Route::delete('/barangays/{barangay}', [BarangayController::class, 'destroy'])->name('admin.barangays.destroy');

    Route::get('/backup-database', [DatabaseBackupController::class, 'backup'])->name('database.backup');
});

// RPT
Route::middleware('auth')->group(function () {
    Route::get('/rpt', [RPTController::class, 'index'])->name('rpt.index');
    Route::get('/rpt/faas_list', [RPTController::class, 'faas_list'])->name('rpt.faas_list');
    Route::get('/rpt/faas-view/{id}', [RPTController::class, 'faas_view'])->name('rpt.faas_view');
    Route::get('/rpt/land', [RPTController::class, 'land'])->name('rpt.faas_entry.land');
    Route::get('/rpt/building', [RPTController::class, 'building'])->name('rpt.faas_entry.building');
    Route::get('/rpt/machine', [RPTController::class, 'machine'])->name('rpt.faas_entry.machine');
    Route::get('/rpt/taxdec_based', [RPTController::class, 'taxdec_based'])->name('rpt.faas_entry.taxdec_based');
    Route::post('/rpt', [RPTController::class, 'store'])->name('rpt.store');
    Route::get('/rpt/get-actual-uses', [RPTController::class, 'get_actual_uses'])->name('rpt.get_actual_uses');
    Route::get('/rpt/get-unit-value', [RPTController::class, 'get_unit_value'])->name('rpt.get_unit_value');
    Route::get('/rpt/get-assessment-level', [RPTController::class, 'get_assessment_level'])->name('rpt.get_assessment_level');
    Route::get('/rpta_settings', [RPTA_SettingsController::class, 'index'])->name('rpt.rpta_settings.index');
    Route::get('/rpta_settings/actual_use', [RPTA_SettingsController::class, 'actual_use'])->name('rpt.rpta_settings.actual_use');

    Route::prefix('rpt/gis')->name('rpt.gis.')->group(function () {
        Route::get('/', [GISController::class, 'index'])->name('index');
        Route::get('/geometries', [GISController::class, 'getGeometries'])->name('get_geometries');
        Route::post('/update-geometry', [GISController::class, 'updateGeometry'])->name('update_geometry');
    });

    Route::prefix('rpt/td')->name('rpt.td.')->group(function () {
        Route::get('/create', [TaxDeclarationController::class, 'create'])->name('create');
        Route::post('/', [TaxDeclarationController::class, 'store'])->name('store');
        Route::put('/{id}', [TaxDeclarationController::class, 'update'])->name('update');
        Route::get('/{id}/edit', [TaxDeclarationController::class, 'edit'])->name('edit');
        Route::delete('/{id}', [TaxDeclarationController::class, 'destroy'])->name('destroy');
        Route::delete('/{id}/component', [TaxDeclarationController::class, 'deleteComponent'])->name('delete_component');
        Route::get('/revision/search', [TaxDeclarationController::class, 'revisionSearch'])->name('revision_search');
        Route::get('/{id}/revise/{type}/{component_id}', [TaxDeclarationController::class, 'reviseComponent'])->name('revise_component');
        Route::post('/{id}/revise/{type}/{component_id}', [TaxDeclarationController::class, 'updateRevision'])->name('update_revision');
        Route::get('/{id}/history', [TaxDeclarationController::class, 'revisionHistory'])->name('history');
        Route::get('/{id}/revision-type', [TaxDeclarationController::class, 'selectRevisionType'])->name('select_revision_type');
        Route::post('/{id}/process-revision', [TaxDeclarationController::class, 'processRevision'])->name('process_revision');
        Route::get('/{id}/transfer', [TaxDeclarationController::class, 'showTransferForm'])->name('transfer');
        Route::post('/{id}/transfer', [TaxDeclarationController::class, 'processTransfer'])->name('process_transfer');
        Route::get('/{id}/add-land', [TaxDeclarationController::class, 'addLand'])->name('add_land');
        Route::post('/{id}/land', [TaxDeclarationController::class, 'storeLand'])->name('store_land');
        Route::get('/{id}/add-building', [TaxDeclarationController::class, 'addBuilding'])->name('add_building');
        Route::post('/{id}/building', [TaxDeclarationController::class, 'storeBuilding'])->name('store_building');
        Route::get('/{id}/add-machine', [TaxDeclarationController::class, 'addMachine'])->name('add_machine');
        Route::post('/{id}/machine', [TaxDeclarationController::class, 'storeMachine'])->name('store_machine');
        Route::get('/{id}/machine/{machine_id}/edit', [TaxDeclarationController::class, 'editMachine'])->name('edit_machine');
        Route::put('/{id}/machine/{machine_id}', [TaxDeclarationController::class, 'updateMachine'])->name('update_machine');
        Route::get('/api/search/{td_no}', [TaxDeclarationController::class, 'apiSearch'])->name('api_search');
        Route::post('/{id}/submit-review', [TaxDeclarationController::class, 'submitReview'])->name('submit_review');
        Route::post('/{id}/approve', [TaxDeclarationController::class, 'approve'])->name('approve');
        Route::post('/{id}/cancel', [TaxDeclarationController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/upload-attachment', [TaxDeclarationController::class, 'uploadAttachment'])->name('upload_attachment');
        Route::post('/{id}/update-inspection', [TaxDeclarationController::class, 'updateInspection'])->name('update_inspection');
        Route::get('/{id}/print', [TaxDeclarationController::class, 'printTD'])->name('print');
    });

    Route::prefix('actual-use')->name('rpt.actual-use.')->group(function () {
        Route::get('/', [RptAuController::class, 'index'])->name('index');
        Route::post('/', [RptAuController::class, 'store'])->name('store');
        Route::get('/{rptAu}', [RptAuController::class, 'show'])->name('show');
        Route::post('/{rptAu}', [RptAuController::class, 'update'])->name('update');
        Route::delete('/{rptAu}', [RptAuController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('additional-items')->name('rpt.additional-items.')->group(function () {
        Route::get('/', [AdditionalItemController::class, 'index'])->name('index');
        Route::post('/', [AdditionalItemController::class, 'store'])->name('store');
        Route::get('/{additionalItem}', [AdditionalItemController::class, 'show'])->name('show');
        Route::put('/{additionalItem}', [AdditionalItemController::class, 'update'])->name('update');
        Route::delete('/{additionalItem}', [AdditionalItemController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('assessment-levels')->name('rpt.assessment-levels.')->group(function () {
        Route::get('/', [AssessmentLevelController::class, 'index'])->name('index');
        Route::post('/', [AssessmentLevelController::class, 'store'])->name('store');
        Route::get('/{assessmentLevel}', [AssessmentLevelController::class, 'show'])->name('show');
        Route::put('/{assessmentLevel}', [AssessmentLevelController::class, 'update'])->name('update');
        Route::delete('/{assessmentLevel}', [AssessmentLevelController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('classifications')->name('rpt.classifications.')->group(function () {
        Route::get('/', [ClassificationController::class, 'index'])->name('index');
        Route::post('/', [ClassificationController::class, 'store'])->name('store');
        Route::get('/{classification}', [ClassificationController::class, 'show'])->name('show');
        Route::put('/{classification}', [ClassificationController::class, 'update'])->name('update');
        Route::delete('/{classification}', [ClassificationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('depreciation-rates')->name('rpt.depreciation-rates.')->group(function () {
        Route::get('/', [DepreciationRateBldgController::class, 'index'])->name('index');
        Route::post('/', [DepreciationRateBldgController::class, 'store'])->name('store');
        Route::get('/{depreciationRate}', [DepreciationRateBldgController::class, 'show'])->name('show');
        Route::put('/{depreciationRate}', [DepreciationRateBldgController::class, 'update'])->name('update');
        Route::delete('/{depreciationRate}', [DepreciationRateBldgController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('owners')->name('rpt.owners.')->group(function () {
        Route::get('/', [OwnerController::class, 'index'])->name('index');
        Route::post('/', [OwnerController::class, 'store'])->name('store');
        Route::get('/{owner}', [OwnerController::class, 'show'])->name('show');
        Route::put('/{owner}', [OwnerController::class, 'update'])->name('update');
        Route::delete('/{owner}', [OwnerController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('other-improvements')->name('rpt.other-improvements.')->group(function () {
        Route::get('/', [OtherImprovementController::class, 'index'])->name('index');
        Route::post('/', [OtherImprovementController::class, 'store'])->name('store');
        Route::get('/{improvement}', [OtherImprovementController::class, 'show'])->name('show');
        Route::put('/{improvement}', [OtherImprovementController::class, 'update'])->name('update');
        Route::delete('/{improvement}', [OtherImprovementController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('signatories')->name('rpt.signatories.')->group(function () {
        Route::get('/', [RptaSignatoryController::class, 'index'])->name('index');
        Route::post('/', [RptaSignatoryController::class, 'store'])->name('store');
        Route::get('/{signatory}', [RptaSignatoryController::class, 'show'])->name('show');
        Route::put('/{signatory}', [RptaSignatoryController::class, 'update'])->name('update');
        Route::delete('/{signatory}', [RptaSignatoryController::class, 'destroy'])->name('destroy');
        Route::post('/update-revision-year', [RptaSignatoryController::class, 'updateRevisionYear'])->name('update-revision-year');
        Route::post('/update-rc-signatory', [RptaSignatoryController::class, 'updateRcSignatory'])->name('update-rc-signatory');
    });

    Route::prefix('transaction-codes')->name('rpt.transaction_code.')->group(function () {
        Route::get('/', [RptTransactionCodeController::class, 'index'])->name('index');
        Route::post('/', [RptTransactionCodeController::class, 'store'])->name('store');
        Route::get('/{transaction_code}', [RptTransactionCodeController::class, 'show'])->name('show');
        Route::put('/{transaction_code}', [RptTransactionCodeController::class, 'update'])->name('update');
        Route::delete('/{transaction_code}', [RptTransactionCodeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('general-revision')->name('rpt.general_revision.')->group(function () {
        Route::get('/', [RptaGenRevController::class, 'index'])->name('index');
        Route::get('/years', [RptaGenRevController::class, 'getMaxRevisionYear'])->name('years');
        Route::post('/process', [RptaGenRevController::class, 'processRevision'])->name('process');
        Route::get('/list', [RptaGenRevController::class, 'getRevisions'])->name('list');
        Route::delete('/{id}/cancel', [RptaGenRevController::class, 'cancelRevision'])->name('cancel');
    });

    Route::prefix('rpt/reports')->name('rpt.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        $reports = [
            'parcel-list' => 'parcelList',
            'rpu-list' => 'rpuList',
            'cancelled-list' => 'cancelledList',
            'faas-summary' => 'faasSummary',
            'td-summary' => 'tdSummary',
            'taxable-properties' => 'taxableProperties',
            'ownership-history' => 'ownershipHistory',
            'transfer-summary' => 'transferSummary',
            'multiple-owners' => 'multipleOwners',
            'td-audit-log' => 'tdAuditLog',
            'global-transaction-log' => 'globalTransactionLog',
            'user-activity-audit' => 'userActivityAudit',
        ];
        foreach ($reports as $slug => $method) {
            $name = str_replace('-', '_', $slug);
            Route::get("/{$slug}", [ReportController::class, $method])->name($name);
            Route::get("/{$slug}/export/pdf", [ReportController::class, 'export' . ucfirst(lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $slug))))) . 'PDF'])->name("{$name}.export.pdf");
        }
    });
});

// Treasury
Route::get('/treasury', fn() => view('modules.treasury.index'))->name('treasury.index');
Route::get('/treasury/bpls-payment', [BplsPaymentController::class, 'index'])->name('bpls_payment');

require __DIR__ . '/auth.php';