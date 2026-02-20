<?php

use App\Http\Controllers\ProfileController;

//Admin Controllers
use App\Http\Livewire\Admin\AdminDashboard;
use App\Http\Livewire\Admin\AccountsManager;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\BarangayController;
use App\Http\Controllers\Admin\DatabaseBackupController;
use Illuminate\Http\Request;
//HR Controllers
use App\Http\Controllers\Hr\HumanResourcesController;

//RPT Controllers
use App\Http\Controllers\RPT\RPTController;
use App\Http\Controllers\RPT\TaxDeclarationController;
use App\Http\Controllers\RPT\ReportController;
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

//BPLS CONTROLLERS
use App\Http\Controllers\BplsPaymentController;
use App\Http\Controllers\BplsController;
use App\Http\Controllers\BusinessEntriesController;
use App\Http\Controllers\BusinessListController;
use App\Http\Controllers\Bpls\FeeRuleController;

// ============================================================================
// BPLS TAXES AND FEES CALCULATION ROUTING
// ============================================================================
Route::prefix('bpls/fee-rules')->name('bpls.fee-rules.')->middleware(['auth', 'verified'])->group(function () {

    // ── Blade page ────────────────────────────────────────────────────────
    // Must be declared FIRST before the wildcard /{feeRule} catches it.
    Route::get('/manage', function () {
        return view('modules.bpls.fee-rules.index');
    })->name('manage');                                                          // GET /bpls/fee-rules/manage

    // ── CRUD (JSON API — called by Alpine.js in the blade) ────────────────
    Route::get('/', [FeeRuleController::class, 'index'])->name('index');    // GET    /bpls/fee-rules
    Route::post('/', [FeeRuleController::class, 'store'])->name('store');    // POST   /bpls/fee-rules
    Route::get('/{feeRule}', [FeeRuleController::class, 'show'])->name('show');     // GET    /bpls/fee-rules/{id}
    Route::put('/{feeRule}', [FeeRuleController::class, 'update'])->name('update');   // PUT    /bpls/fee-rules/{id}
    Route::delete('/{feeRule}', [FeeRuleController::class, 'destroy'])->name('destroy');  // DELETE /bpls/fee-rules/{id}

    // ── Utility (all before /{feeRule} wildcard) ──────────────────────────
    Route::post('/reorder', [FeeRuleController::class, 'reorder'])->name('reorder');        // POST /bpls/fee-rules/reorder
    Route::post('/reset-defaults', [FeeRuleController::class, 'resetDefaults'])->name('reset-defaults'); // POST /bpls/fee-rules/reset-defaults
    Route::post('/compute', [FeeRuleController::class, 'compute'])->name('compute');        // POST /bpls/fee-rules/compute

    // Toggle (needs ID so it sits after, safe because it has the /toggle suffix)
    Route::post('/{feeRule}/toggle', [FeeRuleController::class, 'toggle'])->name('toggle');         // POST /bpls/fee-rules/{id}/toggle

});
// END OF BPLS TAXES AND FEES CALCULATION ROUTING

// ============================================================================
// BPLS PUBLIC/API ROUTES (NO AUTH REQUIRED - FOR SEARCH FUNCTIONALITY)
// ============================================================================
// IMPORTANT: These routes are placed OUTSIDE auth middleware to allow
// search functionality to work on the form. The form itself is protected
// by auth, but these API endpoints need to be accessible for AJAX calls.
// ============================================================================
Route::prefix('bpls')->name('bpls.')->group(function () {
    // Public search endpoints (used by Alpine.js for live search)
    Route::get('/search-owner', [BusinessEntriesController::class, 'searchOwner'])->name('search-owner');       // GET /bpls/search-owner?q=term
    Route::get('/search-business', [BusinessEntriesController::class, 'searchBusiness'])->name('search-business'); // GET /bpls/search-business?q=term
});

// ============================================================================
// BPLS PROTECTED ROUTES (REQUIRES AUTHENTICATION)
// All business entry forms and management routes require login
// ============================================================================
Route::middleware('auth')->group(function () {
    Route::prefix('bpls')->name('bpls.')->group(function () {
        // Main BPLS Dashboard
        Route::get('/', [BplsController::class, 'index'])->name('index');                                      // GET /bpls

        // Business Entries - CRUD operations
        Route::get('/business-entries', [BusinessEntriesController::class, 'index'])->name('business-entries.index');   // GET /bpls/business-entries
        Route::post('/business-entries', [BusinessEntriesController::class, 'store'])->name('business-entries.store');   // POST /bpls/business-entries

        // NOTE: Search routes are defined ABOVE the auth middleware to allow public access
        // They are at: /bpls/search-owner and /bpls/search-business

        // Business List - Management
        Route::get('/business-list', [BusinessListController::class, 'index'])->name('business-list.index');           // GET /bpls/business-list
        Route::get('/business-list/search', [BusinessListController::class, 'search'])->name('business-list.search');   // GET /bpls/business-list/search?q=term
        Route::post('/business-list/{entry}/assess', [BusinessListController::class, 'assess'])->name('business-list.assess'); // POST /bpls/business-list/{id}/assess
    });
});

// ============================================================================
// TREASURY ROUTES
// ============================================================================
// Just displaying page
Route::get('/treasury/bpls-payment', [BplsPaymentController::class, 'index'])->name('bpls_payment');           // GET /treasury/bpls-payment
// Just displaying page
Route::get('/treasury', function () {
    return view('modules.treasury.index');
})->name('treasury.index');                                                                                      // GET /treasury

// ============================================================================
// PUBLIC ROUTES
// ============================================================================
Route::get('/', function () {
    return view('welcome');
});                                                                                                              // GET /

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');                                                          // GET /dashboard

// ============================================================================
// PROFILE ROUTES (AUTH PROTECTED)
// ============================================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');                             // GET /profile
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');                       // PATCH /profile
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');                    // DELETE /profile
});

// ============================================================================
// HR ROUTES
// ============================================================================
Route::middleware('auth')->group(function () {
    Route::get('/employee-info/create', [HumanResourcesController::class, 'create'])->name('employee-info.create'); // GET /employee-info/create
    Route::post('/employee-info', [HumanResourcesController::class, 'store'])->name('employee-info.store');         // POST /employee-info
});

// ============================================================================
// ADMIN - ACCOUNTS MANAGEMENT
// ============================================================================
Route::middleware(['auth'])->group(function () {

    // Use Livewire component for admin dashboard
    Route::get('/admin/dashboard', AdminDashboard::class)
        ->name('admin.dashboard.index');                                                                          // GET /admin/dashboard

    // Use Livewire component for accounts management
    Route::get('/accounts', AccountsManager::class)
        ->name('accounts.index');                                                                                  // GET /accounts

    // DEPARTMENT MANAGEMENT
    Route::get('/departments', [DepartmentController::class, 'index'])->name('admin.departments.index');          // GET /departments
    Route::post('/departments', [DepartmentController::class, 'store'])->name('admin.departments.store');         // POST /departments
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('admin.departments.update'); // PUT /departments/{id}
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('admin.departments.destroy'); // DELETE /departments/{id}

    // BARANGAY MANAGEMENT
    Route::get('/barangays', [BarangayController::class, 'index'])->name('admin.barangays.index');                // GET /barangays
    Route::post('/barangays', [BarangayController::class, 'store'])->name('admin.barangays.store');               // POST /barangays
    Route::put('/barangays/{barangay}', [BarangayController::class, 'update'])->name('admin.barangays.update');   // PUT /barangays/{id}
    Route::delete('/barangays/{barangay}', [BarangayController::class, 'destroy'])->name('admin.barangays.destroy'); // DELETE /barangays/{id}

    // DATABASE BACKUP
    Route::get('/backup-database', [DatabaseBackupController::class, 'backup'])
        ->name('database.backup');                                                                                 // GET /backup-database
});

// ============================================================================
// RPT (REAL PROPERTY TAX) ROUTES
// ============================================================================
Route::middleware('auth')->group(function () {
    // Dashboard RPT
    Route::get('/rpt', [RPTController::class, 'index'])->name('rpt.index');                                       // GET /rpt

    // Faas List RPT
    Route::get('/rpt/faas_list', [RPTController::class, 'faas_list'])->name('rpt.faas_list');                     // GET /rpt/faas_list
    Route::get('/rpt/faas-view/{id}', [RPTController::class, 'faas_view'])->name('rpt.faas_view');                 // GET /rpt/faas-view/{id}

    // Tax Declaration Management
    Route::prefix('rpt/td')->name('rpt.td.')->group(function () {
        Route::get('/create', [TaxDeclarationController::class, 'create'])->name('create');                         // GET /rpt/td/create
        Route::post('/', [TaxDeclarationController::class, 'store'])->name('store');                                // POST /rpt/td
        Route::get('/{id}/edit', [TaxDeclarationController::class, 'edit'])->name('edit');                          // GET /rpt/td/{id}/edit
        Route::delete('/{id}', [TaxDeclarationController::class, 'destroy'])->name('destroy');                      // DELETE /rpt/td/{id}
        Route::delete('/{id}/component', [TaxDeclarationController::class, 'deleteComponent'])->name('delete_component'); // DELETE /rpt/td/{id}/component

        Route::get('/create', [TaxDeclarationController::class, 'create'])->name('create');
        Route::post('/', [TaxDeclarationController::class, 'store'])->name('store');
        Route::put('/{id}', [TaxDeclarationController::class, 'update'])->name('update');
        Route::get('/{id}/edit', [TaxDeclarationController::class, 'edit'])->name('edit');
        Route::delete('/{id}', [TaxDeclarationController::class, 'destroy'])->name('destroy');
        Route::delete('/{id}/component', [TaxDeclarationController::class, 'deleteComponent'])->name('delete_component');
        
        // Revisions
        Route::get('/revision/search', [TaxDeclarationController::class, 'revisionSearch'])->name('revision_search'); // GET /rpt/td/revision/search
        Route::get('/{id}/revise/{type}/{component_id}', [TaxDeclarationController::class, 'reviseComponent'])->name('revise_component'); // GET /rpt/td/{id}/revise/{type}/{component_id}
        Route::post('/{id}/revise/{type}/{component_id}', [TaxDeclarationController::class, 'updateRevision'])->name('update_revision'); // POST /rpt/td/{id}/revise/{type}/{component_id}
        Route::get('/{id}/history', [TaxDeclarationController::class, 'revisionHistory'])->name('history');          // GET /rpt/td/{id}/history
        Route::get('/{id}/revision-type', [TaxDeclarationController::class, 'selectRevisionType'])->name('select_revision_type'); // GET /rpt/td/{id}/revision-type
        Route::post('/{id}/process-revision', [TaxDeclarationController::class, 'processRevision'])->name('process_revision'); // POST /rpt/td/{id}/process-revision
        Route::get('/{id}/transfer', [TaxDeclarationController::class, 'showTransferForm'])->name('transfer');       // GET /rpt/td/{id}/transfer
        Route::post('/{id}/transfer', [TaxDeclarationController::class, 'processTransfer'])->name('process_transfer'); // POST /rpt/td/{id}/transfer

        // Component Addition
        Route::get('/{id}/add-land', [TaxDeclarationController::class, 'addLand'])->name('add_land');                // GET /rpt/td/{id}/add-land
        Route::post('/{id}/land', [TaxDeclarationController::class, 'storeLand'])->name('store_land');               // POST /rpt/td/{id}/land
        Route::get('/{id}/add-building', [TaxDeclarationController::class, 'addBuilding'])->name('add_building');    // GET /rpt/td/{id}/add-building
        Route::post('/{id}/building', [TaxDeclarationController::class, 'storeBuilding'])->name('store_building');   // POST /rpt/td/{id}/building
        Route::get('/{id}/add-machine', [TaxDeclarationController::class, 'addMachine'])->name('add_machine');       // GET /rpt/td/{id}/add-machine
        Route::post('/{id}/machine', [TaxDeclarationController::class, 'storeMachine'])->name('store_machine');      // POST /rpt/td/{id}/machine
        Route::get('/{id}/add-land', [TaxDeclarationController::class, 'addLand'])->name('add_land');
        Route::post('/{id}/land', [TaxDeclarationController::class, 'storeLand'])->name('store_land');
        Route::get('/{id}/add-building', [TaxDeclarationController::class, 'addBuilding'])->name('add_building');
        Route::post('/{id}/building', [TaxDeclarationController::class, 'storeBuilding'])->name('store_building');
        Route::get('/{id}/add-machine', [TaxDeclarationController::class, 'addMachine'])->name('add_machine');
        Route::post('/{id}/machine', [TaxDeclarationController::class, 'storeMachine'])->name('store_machine');
        Route::get('/{id}/machine/{machine_id}/edit', [TaxDeclarationController::class, 'editMachine'])->name('edit_machine');
        Route::put('/{id}/machine/{machine_id}', [TaxDeclarationController::class, 'updateMachine'])->name('update_machine');

        // API
        Route::get('/api/search/{td_no}', [TaxDeclarationController::class, 'apiSearch'])->name('api_search');       // GET /rpt/td/api/search/{td_no}

        // Workflow Actions
        Route::post('/{id}/submit-review', [TaxDeclarationController::class, 'submitReview'])->name('submit_review'); // POST /rpt/td/{id}/submit-review
        Route::post('/{id}/approve', [TaxDeclarationController::class, 'approve'])->name('approve');                  // POST /rpt/td/{id}/approve
        Route::post('/{id}/cancel', [TaxDeclarationController::class, 'cancel'])->name('cancel');                     // POST /rpt/td/{id}/cancel
        Route::post('/{id}/upload-attachment', [TaxDeclarationController::class, 'uploadAttachment'])->name('upload_attachment'); // POST /rpt/td/{id}/upload-attachment
        Route::post('/{id}/update-inspection', [TaxDeclarationController::class, 'updateInspection'])->name('update_inspection'); // POST /rpt/td/{id}/update-inspection
        Route::get('/{id}/print', [TaxDeclarationController::class, 'printTD'])->name('print');                       // GET /rpt/td/{id}/print
    });

    // Fast Entry
    Route::get('/rpt/land', [RPTController::class, 'land'])->name('rpt.faas_entry.land');                            // GET /rpt/land
    Route::get('/rpt/building', [RPTController::class, 'building'])->name('rpt.faas_entry.building');                // GET /rpt/building
    Route::get('/rpt/machine', [RPTController::class, 'machine'])->name('rpt.faas_entry.machine');                   // GET /rpt/machine
    Route::get('/rpt/taxdec_based', [RPTController::class, 'taxdec_based'])->name('rpt.faas_entry.taxdec_based');    // GET /rpt/taxdec_based

    // RPT CRUD and Utilities
    Route::post('/rpt', [RPTController::class, 'store'])->name('rpt.store');                                         // POST /rpt
    Route::get('/rpt/get-actual-uses', [RPTController::class, 'get_actual_uses'])->name('rpt.get_actual_uses');      // GET /rpt/get-actual-uses
    Route::get('/rpt/get-unit-value', [RPTController::class, 'get_unit_value'])->name('rpt.get_unit_value');         // GET /rpt/get-unit-value
    Route::get('/rpt/get-assessment-level', [RPTController::class, 'get_assessment_level'])->name('rpt.get_assessment_level'); // GET /rpt/get-assessment-level

    // RPTA Settings
    Route::get('/rpta_settings', [RPTA_SettingsController::class, 'index'])->name('rpt.rpta_settings.index');        // GET /rpta_settings
    Route::get('/rpta_settings/actual_use', [RPTA_SettingsController::class, 'actual_use'])->name('rpt.rpta_settings.actual_use'); // GET /rpta_settings/actual_use
    // RPTA Settings
    Route::get('/rpta_settings', [RPTA_SettingsController::class, 'index'])->name('rpt.rpta_settings.index');
    Route::get('/rpta_settings/actual_use', [RPTA_SettingsController::class, 'actual_use'])->name('rpt.rpta_settings.actual_use');

    // GIS & Mapping
    Route::prefix('rpt/gis')->name('rpt.gis.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RPT\GISController::class, 'index'])->name('index');
        Route::get('/geometries', [\App\Http\Controllers\RPT\GISController::class, 'getGeometries'])->name('get_geometries');
        Route::post('/update-geometry', [\App\Http\Controllers\RPT\GISController::class, 'updateGeometry'])->name('update_geometry');
    });


    // Actual Use Routes
    Route::get('/actual-use', [RptAuController::class, 'index'])->name('rpt.actual-use.index');                      // GET /actual-use
    Route::post('/actual-use', [RptAuController::class, 'store'])->name('rpt.actual-use.store');                     // POST /actual-use
    Route::get('/actual-use/{rptAu}', [RptAuController::class, 'show'])->name('rpt.actual-use.show');                // GET /actual-use/{id}
    Route::post('/actual-use/{rptAu}', [RptAuController::class, 'update'])->name('rpt.actual-use.update');           // POST /actual-use/{id}
    Route::delete('/actual-use/{rptAu}', [RptAuController::class, 'destroy'])->name('rpt.actual-use.destroy');       // DELETE /actual-use/{id}

    // Additional Item Routes
    Route::get('/additional-items', [AdditionalItemController::class, 'index'])->name('rpt.additional-items.index'); // GET /additional-items
    Route::post('/additional-items', [AdditionalItemController::class, 'store'])->name('rpt.additional-items.store'); // POST /additional-items
    Route::get('/additional-items/{additionalItem}', [AdditionalItemController::class, 'show'])->name('rpt.additional-items.show'); // GET /additional-items/{id}
    Route::put('/additional-items/{additionalItem}', [AdditionalItemController::class, 'update'])->name('rpt.additional-items.update'); // PUT /additional-items/{id}
    Route::delete('/additional-items/{additionalItem}', [AdditionalItemController::class, 'destroy'])->name('rpt.additional-items.destroy'); // DELETE /additional-items/{id}

    // Assessment Level Routes
    Route::get('/assessment-levels', [AssessmentLevelController::class, 'index'])->name('rpt.assessment-levels.index'); // GET /assessment-levels
    Route::post('/assessment-levels', [AssessmentLevelController::class, 'store'])->name('rpt.assessment-levels.store'); // POST /assessment-levels
    Route::put('/assessment-levels/{assessmentLevel}', [AssessmentLevelController::class, 'update'])->name('rpt.assessment-levels.update'); // PUT /assessment-levels/{id}
    Route::delete('/assessment-levels/{assessmentLevel}', [AssessmentLevelController::class, 'destroy'])->name('rpt.assessment-levels.destroy'); // DELETE /assessment-levels/{id}
    Route::get('/assessment-levels/{assessmentLevel}', [AssessmentLevelController::class, 'show'])->name('rpt.assessment-levels.show'); // GET /assessment-levels/{id}

    // Classification Routes
    Route::get('/classifications', [ClassificationController::class, 'index'])->name('rpt.classifications.index');    // GET /classifications
    Route::post('/classifications', [ClassificationController::class, 'store'])->name('rpt.classifications.store');    // POST /classifications
    Route::get('/classifications/{classification}', [ClassificationController::class, 'show'])->name('rpt.classifications.show'); // GET /classifications/{id}
    Route::put('/classifications/{classification}', [ClassificationController::class, 'update'])->name('rpt.classifications.update'); // PUT /classifications/{id}
    Route::delete('/classifications/{classification}', [ClassificationController::class, 'destroy'])->name('rpt.classifications.destroy'); // DELETE /classifications/{id}

    // Depreciation Rate for Building Routes
    Route::get('/depreciation-rates', [DepreciationRateBldgController::class, 'index'])->name('rpt.depreciation-rates.index'); // GET /depreciation-rates
    Route::post('/depreciation-rates', [DepreciationRateBldgController::class, 'store'])->name('rpt.depreciation-rates.store'); // POST /depreciation-rates
    Route::get('/depreciation-rates/{depreciationRate}', [DepreciationRateBldgController::class, 'show'])->name('rpt.depreciation-rates.show'); // GET /depreciation-rates/{id}
    Route::put('/depreciation-rates/{depreciationRate}', [DepreciationRateBldgController::class, 'update'])->name('rpt.depreciation-rates.update'); // PUT /depreciation-rates/{id}
    Route::delete('/depreciation-rates/{depreciationRate}', [DepreciationRateBldgController::class, 'destroy'])->name('rpt.depreciation-rates.destroy'); // DELETE /depreciation-rates/{id}

    // Owner Selection Routes
    Route::get('/owners', [OwnerController::class, 'index'])->name('rpt.owners.index');                               // GET /owners
    Route::post('/owners', [OwnerController::class, 'store'])->name('rpt.owners.store');                              // POST /owners
    Route::get('/owners/{owner}', [OwnerController::class, 'show'])->name('rpt.owners.show');                         // GET /owners/{id}
    Route::put('/owners/{owner}', [OwnerController::class, 'update'])->name('rpt.owners.update');                     // PUT /owners/{id}
    Route::delete('/owners/{owner}', [OwnerController::class, 'destroy'])->name('rpt.owners.destroy');                // DELETE /owners/{id}

    // Other Improvement Routes
    Route::get('/other-improvements', [OtherImprovementController::class, 'index'])->name('rpt.other-improvements.index'); // GET /other-improvements
    Route::post('/other-improvements', [OtherImprovementController::class, 'store'])->name('rpt.other-improvements.store'); // POST /other-improvements
    Route::get('/other-improvements/{improvement}', [OtherImprovementController::class, 'show'])->name('rpt.other-improvements.show'); // GET /other-improvements/{id}
    Route::put('/other-improvements/{improvement}', [OtherImprovementController::class, 'update'])->name('rpt.other-improvements.update'); // PUT /other-improvements/{id}
    Route::delete('/other-improvements/{improvement}', [OtherImprovementController::class, 'destroy'])->name('rpt.other-improvements.destroy'); // DELETE /other-improvements/{id}

    // RPTA Signatories Routes
    Route::get('/signatories', [RptaSignatoryController::class, 'index'])->name('rpt.signatories.index');             // GET /signatories
    Route::post('/signatories', [RptaSignatoryController::class, 'store'])->name('rpt.signatories.store');            // POST /signatories
    Route::get('/signatories/{signatory}', [RptaSignatoryController::class, 'show'])->name('rpt.signatories.show');   // GET /signatories/{id}
    Route::put('/signatories/{signatory}', [RptaSignatoryController::class, 'update'])->name('rpt.signatories.update'); // PUT /signatories/{id}
    Route::delete('/signatories/{signatory}', [RptaSignatoryController::class, 'destroy'])->name('rpt.signatories.destroy'); // DELETE /signatories/{id}
    Route::post('/signatories/update-revision-year', [RptaSignatoryController::class, 'updateRevisionYear'])->name('rpt.signatories.update-revision-year'); // POST /signatories/update-revision-year
    Route::post('/signatories/update-rc-signatory', [RptaSignatoryController::class, 'updateRcSignatory'])->name('rpt.signatories.update-rc-signatory'); // POST /signatories/update-rc-signatory

    // Transaction Code Routes
    Route::get('/transaction-codes', [RptTransactionCodeController::class, 'index'])->name('rpt.transaction_code.index'); // GET /transaction-codes
    Route::post('/transaction-codes', [RptTransactionCodeController::class, 'store'])->name('rpt.transaction_code.store'); // POST /transaction-codes
    Route::get('/transaction-codes/{transaction_code}', [RptTransactionCodeController::class, 'show'])->name('rpt.transaction_code.show'); // GET /transaction-codes/{id}
    Route::put('/transaction-codes/{transaction_code}', [RptTransactionCodeController::class, 'update'])->name('rpt.transaction_code.update'); // PUT /transaction-codes/{id}
    Route::delete('/transaction-codes/{transaction_code}', [RptTransactionCodeController::class, 'destroy'])->name('rpt.transaction_code.destroy'); // DELETE /transaction-codes/{id}

    // General Revision Routes
    Route::get('/general-revision', [RptaGenRevController::class, 'index'])->name('rpt.general_revision.index');       // GET /general-revision
    Route::get('/general-revision/years', [RptaGenRevController::class, 'getMaxRevisionYear'])->name('rpt.general_revision.years'); // GET /general-revision/years
    Route::post('/general-revision/process', [RptaGenRevController::class, 'processRevision'])->name('rpt.general_revision.process'); // POST /general-revision/process
    Route::get('/general-revision/list', [RptaGenRevController::class, 'getRevisions'])->name('rpt.general_revision.list'); // GET /general-revision/list
    Route::delete('/general-revision/{id}/cancel', [RptaGenRevController::class, 'cancelRevision'])->name('rpt.general_revision.cancel'); // DELETE /general-revision/{id}/cancel

    // RPT Reports
    Route::prefix('rpt/reports')->name('rpt.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');                                           // GET /rpt/reports
        Route::get('/parcel-list', [ReportController::class, 'parcelList'])->name('parcel_list');                     // GET /rpt/reports/parcel-list
        Route::get('/rpu-list', [ReportController::class, 'rpuList'])->name('rpu_list');                               // GET /rpt/reports/rpu-list
        Route::get('/cancelled-list', [ReportController::class, 'cancelledList'])->name('cancelled_list');             // GET /rpt/reports/cancelled-list
        Route::get('/parcel-list/export/pdf', [ReportController::class, 'exportParcelPDF'])->name('parcel_list.export.pdf'); // GET /rpt/reports/parcel-list/export/pdf
        Route::get('/rpu-list/export/pdf', [ReportController::class, 'exportRpuPDF'])->name('rpu_list.export.pdf');    // GET /rpt/reports/rpu-list/export/pdf
        Route::get('/cancelled-list/export/pdf', [ReportController::class, 'exportCancelledPDF'])->name('cancelled_list.export.pdf'); // GET /rpt/reports/cancelled-list/export/pdf

        // Valuation Analysis
        Route::get('/faas-summary', [ReportController::class, 'faasSummary'])->name('faas_summary');                   // GET /rpt/reports/faas-summary
        Route::get('/faas-summary/export/pdf', [ReportController::class, 'exportFaasSummaryPDF'])->name('faas_summary.export.pdf'); // GET /rpt/reports/faas-summary/export/pdf

        Route::get('/td-summary', [ReportController::class, 'tdSummary'])->name('td_summary');                         // GET /rpt/reports/td-summary
        Route::get('/td-summary/export/pdf', [ReportController::class, 'exportTdSummaryPDF'])->name('td_summary.export.pdf'); // GET /rpt/reports/td-summary/export/pdf

        Route::get('/taxable-properties', [ReportController::class, 'taxableProperties'])->name('taxable_properties'); // GET /rpt/reports/taxable-properties
        Route::get('/taxable-properties/export/pdf', [ReportController::class, 'exportTaxablePropertiesPDF'])->name('taxable_properties.export.pdf'); // GET /rpt/reports/taxable-properties/export/pdf

        // Ownership Tracking
        Route::get('/ownership-history', [ReportController::class, 'ownershipHistory'])->name('ownership_history');    // GET /rpt/reports/ownership-history
        Route::get('/ownership-history/export/pdf', [ReportController::class, 'exportOwnershipHistoryPDF'])->name('ownership_history.export.pdf'); // GET /rpt/reports/ownership-history/export/pdf

        Route::get('/transfer-summary', [ReportController::class, 'transferSummary'])->name('transfer_summary');       // GET /rpt/reports/transfer-summary
        Route::get('/transfer-summary/export/pdf', [ReportController::class, 'exportTransferSummaryPDF'])->name('transfer_summary.export.pdf'); // GET /rpt/reports/transfer-summary/export/pdf

        Route::get('/multiple-owners', [ReportController::class, 'multipleOwners'])->name('multiple_owners');          // GET /rpt/reports/multiple-owners
        Route::get('/multiple-owners/export/pdf', [ReportController::class, 'exportMultipleOwnersPDF'])->name('multiple_owners.export.pdf'); // GET /rpt/reports/multiple-owners/export/pdf

        // Audit & History
        Route::get('/td-audit-log', [ReportController::class, 'tdAuditLog'])->name('td_audit_log');                    // GET /rpt/reports/td-audit-log
        Route::get('/td-audit-log/export/pdf', [ReportController::class, 'exportTdAuditLogPDF'])->name('td_audit_log.export.pdf'); // GET /rpt/reports/td-audit-log/export/pdf

        Route::get('/global-transaction-log', [ReportController::class, 'globalTransactionLog'])->name('global_transaction_log'); // GET /rpt/reports/global-transaction-log
        Route::get('/global-transaction-log/export/pdf', [ReportController::class, 'exportGlobalTransactionLogPDF'])->name('global_transaction_log.export.pdf'); // GET /rpt/reports/global-transaction-log/export/pdf

        Route::get('/user-activity-audit', [ReportController::class, 'userActivityAudit'])->name('user_activity_audit'); // GET /rpt/reports/user-activity-audit
        Route::get('/user-activity-audit/export/pdf', [ReportController::class, 'exportUserActivityAuditPDF'])->name('user_activity_audit.export.pdf'); // GET /rpt/reports/user-activity-audit/export/pdf
    });
});

require __DIR__ . '/auth.php';