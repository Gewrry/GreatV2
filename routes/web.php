<?php
// routes/web.php

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
use App\Http\Controllers\Bpls\BplsSettingsController;
use App\Http\Controllers\Bpls\MasterlistController;
use App\Http\Controllers\Settings\OrAssignmentController;
use App\Http\Controllers\AuditLogController;




Route::get('/payment/{entry}/available-ors', [BplsPaymentController::class, 'getAvailableOrNumbers'])
    ->name('bpls.payment.available-ors');

Route::prefix('audit-logs')
    ->middleware(['auth'])   // Add additional middleware here, e.g. 'can:view-audit-logs'
    ->name('audit-logs.')
    ->group(function () {

        // ── Blade page ────────────────────────────────────────────────────────
        // GET  /audit-logs
        Route::get('/', [AuditLogController::class, 'index'])->name('index');

        // ── JSON data (Alpine.js fetch) ───────────────────────────────────────
        // GET  /audit-logs/data
        Route::get('/data', [AuditLogController::class, 'data'])->name('data');

        // ── Summary stats ─────────────────────────────────────────────────────
        // GET  /audit-logs/stats
        Route::get('/stats', [AuditLogController::class, 'stats'])->name('stats');

        // ── CSV export ────────────────────────────────────────────────────────
        // GET  /audit-logs/export
        Route::get('/export', [AuditLogController::class, 'export'])->name('export');

        // ── Purge old logs ────────────────────────────────────────────────────
        // DELETE /audit-logs/purge   (admin only — add 'can:purge-audit-logs' if needed)
        Route::delete('/purge', [AuditLogController::class, 'purge'])->name('purge');

        // ── Single log detail (JSON) ──────────────────────────────────────────
        // GET  /audit-logs/{auditLog}
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
    });


// ─────────────────────────────────────────────
// Public
// ─────────────────────────────────────────────
Route::get('/', fn() => view('welcome'));
Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

// BPLS public search (no auth needed)
Route::prefix('bpls')->name('bpls.')->group(function () {
    Route::get('/search-owner', [BusinessEntriesController::class, 'searchOwner'])->name('search-owner');
    Route::get('/search-business', [BusinessEntriesController::class, 'searchBusiness'])->name('search-business');
});

// ─────────────────────────────────────────────
// Authenticated Routes
// ─────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Profile ──────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── HR ───────────────────────────────────
    Route::get('/employee-info/create', [HumanResourcesController::class, 'create'])->name('employee-info.create');
    Route::post('/employee-info', [HumanResourcesController::class, 'store'])->name('employee-info.store');

    // ── Admin ────────────────────────────────
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard.index');
    Route::get('/accounts', AccountsManager::class)->name('accounts.index');

    Route::prefix('departments')->name('admin.departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('barangays')->name('admin.barangays.')->group(function () {
        Route::get('/', [BarangayController::class, 'index'])->name('index');
        Route::post('/', [BarangayController::class, 'store'])->name('store');
        Route::put('/{barangay}', [BarangayController::class, 'update'])->name('update');
        Route::delete('/{barangay}', [BarangayController::class, 'destroy'])->name('destroy');
    });

    Route::get('/backup-database', [DatabaseBackupController::class, 'backup'])->name('database.backup');

    // ── Treasury ─────────────────────────────
    Route::get('/treasury', fn() => view('modules.treasury.index'))->name('treasury.index');
    Route::get('/treasury/bpls-payment', [BplsPaymentController::class, 'index'])->name('bpls_payment');

    // ── OR Assignments (General Settings) ────
    Route::prefix('settings/or-assignments')->name('or-assignments.')->group(function () {
        Route::get('/', [OrAssignmentController::class, 'index'])->name('index');
        Route::post('/', [OrAssignmentController::class, 'store'])->name('store');
        Route::get('/{orAssignment}/edit', [OrAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{orAssignment}', [OrAssignmentController::class, 'update'])->name('update');
        Route::delete('/{orAssignment}', [OrAssignmentController::class, 'destroy'])->name('destroy');
    });

    // ── BPLS ─────────────────────────────────
    Route::prefix('bpls')->name('bpls.')->group(function () {

        Route::get('/', [BplsController::class, 'index'])->name('index');

        // Settings — uses BplsSettingsController (no User/cashier queries)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [BplsSettingsController::class, 'index'])->name('index');
            Route::post('/update-general', [BplsSettingsController::class, 'updateGeneral'])->name('update-general');
            Route::post('/update-discount', [BplsSettingsController::class, 'updateDiscount'])->name('update-discount');
            Route::post('/update-permit', [BplsSettingsController::class, 'updatePermit'])->name('update-permit');
            Route::post('/update-receipt', [BplsSettingsController::class, 'updateReceipt'])->name('update-receipt'); // ← add this
        });
        // Business Entries
        Route::prefix('business-entries')->name('business-entries.')->group(function () {
            Route::get('/', [BusinessEntriesController::class, 'index'])->name('index');
            Route::post('/', [BusinessEntriesController::class, 'store'])->name('store');
        });

        // Business List — static routes BEFORE /{entry} wildcard
        Route::prefix('business-list')->name('business-list.')->group(function () {
            Route::get('/', [BusinessListController::class, 'index'])->name('index');
            Route::get('/search', [BusinessListController::class, 'search'])->name('search');
            Route::get('/{entry}', [BusinessListController::class, 'show'])->name('show');
            Route::post('/{entry}/assess', [BusinessListController::class, 'assess'])->name('assess');
            Route::post('/{entry}/approve-payment', [BplsPaymentController::class, 'approvePayment'])->name('approve-payment');
            Route::post('/{entry}/approve-renewal', [BusinessListController::class, 'approveRenewal'])->name('approve-renewal');
            Route::post('/{entry}/change-status', [BusinessListController::class, 'changeStatus'])->name('change-status');
            Route::post('/{entry}/retire', [BusinessListController::class, 'retire'])->name('retire');
            Route::get('/{entry}/retirement-certificate', [BusinessListController::class, 'retirementCertificate'])->name('retirement-certificate');
        });

        // Payment — permit/receipt BEFORE broad /{entry} wildcard
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/{entry}/permit/{payment}', [BplsPaymentController::class, 'permit'])->name('permit');
            Route::get('/{entry}/receipt/{payment}', [BplsPaymentController::class, 'receipt'])->name('receipt');
            Route::post('/{entry}/compute-surcharge', [BplsPaymentController::class, 'computeSurcharge'])->name('compute-surcharge');
            Route::post('/{entry}/validate-or', [BplsPaymentController::class, 'validateOr'])->name('validate-or');
            Route::post('/{entry}/pay', [BplsPaymentController::class, 'pay'])->name('pay');
            Route::get('/{entry}', [BplsPaymentController::class, 'show'])->name('show');
        });

        // Fee Rules — static routes BEFORE /{feeRule} wildcard
        Route::prefix('fee-rules')->name('fee-rules.')->group(function () {
            Route::get('/manage', fn() => view('modules.bpls.fee-rules.index'))->name('manage');
            Route::post('/reorder', [FeeRuleController::class, 'reorder'])->name('reorder');
            Route::post('/reset-defaults', [FeeRuleController::class, 'resetDefaults'])->name('reset-defaults');
            Route::post('/compute', [FeeRuleController::class, 'compute'])->name('compute');
            Route::get('/', [FeeRuleController::class, 'index'])->name('index');
            Route::post('/', [FeeRuleController::class, 'store'])->name('store');
            Route::get('/{feeRule}', [FeeRuleController::class, 'show'])->name('show');
            Route::put('/{feeRule}', [FeeRuleController::class, 'update'])->name('update');
            Route::delete('/{feeRule}', [FeeRuleController::class, 'destroy'])->name('destroy');
            Route::post('/{feeRule}/toggle', [FeeRuleController::class, 'toggle'])->name('toggle');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/masterlist', [MasterlistController::class, 'index'])->name('masterlist.index');
            Route::get('/masterlist/data', [MasterlistController::class, 'data'])->name('masterlist.data');
        });

    });

    // ── RPT ──────────────────────────────────
    Route::prefix('rpt')->name('rpt.')->group(function () {
        Route::get('/', [RPTController::class, 'index'])->name('index');
        Route::get('/faas_list', [RPTController::class, 'faas_list'])->name('faas_list');
        Route::get('/faas-view/{id}', [RPTController::class, 'faas_view'])->name('faas_view');
        Route::get('/land', [RPTController::class, 'land'])->name('faas_entry.land');
        Route::get('/building', [RPTController::class, 'building'])->name('faas_entry.building');
        Route::get('/machine', [RPTController::class, 'machine'])->name('faas_entry.machine');
        Route::get('/taxdec_based', [RPTController::class, 'taxdec_based'])->name('faas_entry.taxdec_based');
        Route::post('/', [RPTController::class, 'store'])->name('store');
        Route::get('/get-actual-uses', [RPTController::class, 'get_actual_uses'])->name('get_actual_uses');
        Route::get('/get-unit-value', [RPTController::class, 'get_unit_value'])->name('get_unit_value');
        Route::get('/get-assessment-level', [RPTController::class, 'get_assessment_level'])->name('get_assessment_level');
        Route::get('/rpta_settings', [RPTA_SettingsController::class, 'index'])->name('rpta_settings.index');
        Route::get('/rpta_settings/actual_use', [RPTA_SettingsController::class, 'actual_use'])->name('rpta_settings.actual_use');

        Route::prefix('gis')->name('gis.')->group(function () {
            Route::get('/', [GISController::class, 'index'])->name('index');
            Route::get('/geometries', [GISController::class, 'getGeometries'])->name('get_geometries');
            Route::post('/update-geometry', [GISController::class, 'updateGeometry'])->name('update_geometry');
        });

        Route::prefix('td')->name('td.')->group(function () {
            Route::get('/create', [TaxDeclarationController::class, 'create'])->name('create');
            Route::get('/revision/search', [TaxDeclarationController::class, 'revisionSearch'])->name('revision_search');
            Route::get('/api/search/{td_no}', [TaxDeclarationController::class, 'apiSearch'])->name('api_search');
            Route::post('/', [TaxDeclarationController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TaxDeclarationController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TaxDeclarationController::class, 'update'])->name('update');
            Route::delete('/{id}', [TaxDeclarationController::class, 'destroy'])->name('destroy');
            Route::delete('/{id}/component', [TaxDeclarationController::class, 'deleteComponent'])->name('delete_component');
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
            Route::post('/{id}/submit-review', [TaxDeclarationController::class, 'submitReview'])->name('submit_review');
            Route::post('/{id}/approve', [TaxDeclarationController::class, 'approve'])->name('approve');
            Route::post('/{id}/cancel', [TaxDeclarationController::class, 'cancel'])->name('cancel');
            Route::post('/{id}/upload-attachment', [TaxDeclarationController::class, 'uploadAttachment'])->name('upload_attachment');
            Route::post('/{id}/update-inspection', [TaxDeclarationController::class, 'updateInspection'])->name('update_inspection');
            Route::get('/{id}/print', [TaxDeclarationController::class, 'printTD'])->name('print');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
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

    // ── RPT Settings ─────────────────────────
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

});

require __DIR__ . '/auth.php';