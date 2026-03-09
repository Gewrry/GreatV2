<?php
// =============================================================================
// routes/web.php
// =============================================================================
// SECTION INDEX:
//   1.  Imports / Use Statements
//   2.  Standalone Routes (outside any prefix group)
//   3.  Public Routes (no auth required)
//   4.  LGU Staff Portal  [middleware: auth, verified]
//       4a.  Profile Management
//       4b.  HR Module
//       4c.  Admin Module
//       4d.  Treasury Module
//       4e.  OR Assignments
//       4f.  BPLS Module
//             4f-i.   Dashboard
//             4f-ii.  Settings
//             4f-iii. Business Entries
//             4f-iv.  Business List
//             4f-v.   Payments
//             4f-vi.  Fee Rules
//             4f-vii. Form Customization
//             4f-viii.Reports
//             4f-ix.  Online Application Review
//             4f-x.   Permit Signatories
//       4g.  RPT Module
//             4g-i.   FAAS / Tax Declaration
//             4g-ii.  GIS
//             4g-iii. RPT Settings Submodules
//             4g-iv.  RPT Reports
//       4h.  Vehicle Franchising Module  ← NEW
//             4h-i.   Franchise Index / CRUD
//             4h-ii.  Prints (Permit, Sticker, OR/CR)
//             4h-iii. Reports
//   5.  Client Portal  [guard: client]
//       5a.  Guest-only (login, register, password reset)
//       5b.  Authenticated Client Routes
//   6.  Public Webhooks (no CSRF)
//   7.  Auth Routes (require __DIR__.'/auth.php')
// =============================================================================

// =============================================================================
// 1. IMPORTS / USE STATEMENTS
// =============================================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Livewire\Admin\AdminDashboard;
use App\Http\Livewire\Admin\AccountsManager;

// Admin Controllers
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\BarangayController;
use App\Http\Controllers\Admin\DatabaseBackupController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ModuleController;

// HR Controllers
use App\Http\Controllers\Hr\HumanResourcesController;

// RPT Controllers
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

// BPLS Controllers
use App\Http\Controllers\Hr\PlantillaController;
use App\Http\Controllers\Hr\RecruitmentController;
use App\Http\Controllers\HR\AppointmentController;
use App\Http\Controllers\HR\Employee201Controller;
use App\Http\Controllers\HR\SalaryGradeController;
use App\Http\Controllers\BplsController;
use App\Http\Controllers\BplsPaymentController;
use App\Http\Controllers\BusinessEntriesController;
use App\Http\Controllers\BusinessListController;
use App\Http\Controllers\Bpls\FeeRuleController;
use App\Http\Controllers\Bpls\BplsSettingsController;
use App\Http\Controllers\Bpls\MasterlistController;
use App\Http\Controllers\FormCustomizationController;
use App\Http\Controllers\BplsBenefitController;



// BPLS Report Controllers
use App\Http\Controllers\Bpls\ComplianceQuarterController;
use App\Http\Controllers\Bpls\ComplianceDateRangeController;
use App\Http\Controllers\Bpls\TaxDelinquentController;
use App\Http\Controllers\Bpls\BusinessScaleCountController;

// BPLS Misc
use App\Http\Controllers\Bpls\BplsPermitSignatoryController;
use App\Http\Controllers\Bpls\Online\BplsApplicationReviewController;
use App\Http\Controllers\BplsDashboardController;

// Settings
use App\Http\Controllers\Settings\OrAssignmentController;

// Audit
use App\Http\Controllers\AuditLogController;

// Vehicle Franchising Controllers  ← NEW
use App\Http\Controllers\VF\VehicleFranchisingController;
use App\Http\Controllers\VF\VfPrintController;
use App\Http\Controllers\VF\VfReportController;

// Client Portal Controllers
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\ApplicationController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\DocumentUploadController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\WalkInPaymentsController;
use App\Http\Controllers\Client\RptApplicationController;
// RPT (Staff)
use App\Http\Controllers\RPT\FaasPropertyController;
use App\Http\Controllers\RPT\PropertyRegistrationController;
use App\Http\Controllers\RPT\TaxDeclarationController;
use App\Http\Controllers\RPT\OnlineApplicationController;
use App\Http\Controllers\RPT\RptDashboardController;
use App\Http\Controllers\RPT\RPTSettingsController;

// =============================================================================
// 2. STANDALONE ROUTES (outside any prefix/middleware group)
// =============================================================================

Route::get('/payment/{entry}/available-ors', [BplsPaymentController::class, 'getAvailableOrNumbers'])
    ->name('bpls.payment.available-ors');

Route::get('/bpls/dashboard/print', [BplsDashboardController::class, 'printView'])
    ->name('bpls.dashboard.print');

Route::post('bpls/settings/update-beneficiary-discounts', [BplsSettingsController::class, 'updateBeneficiaryDiscounts'])
    ->name('bpls.settings.update-beneficiary-discounts');

// =============================================================================
// 3. PUBLIC ROUTES (No Authentication Required)
// =============================================================================

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::prefix('bpls')->name('bpls.')->group(function () {
    Route::get('/search-owner', [BusinessEntriesController::class, 'searchOwner'])->name('search-owner');
    Route::get('/search-business', [BusinessEntriesController::class, 'searchBusiness'])->name('search-business');
});
Route::resource('bpls/benefits', BplsBenefitController::class)
    ->names('bpls.benefits')
    ->except(['create', 'show']);

Route::patch('bpls/benefits/{benefit}/toggle', [BplsBenefitController::class, 'toggleActive'])
    ->name('bpls.benefits.toggle');
// =============================================================================
// 4. LGU STAFF PORTAL  [middleware: auth + verified]
// =============================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // =========================================================================
    // 4a. AUDIT LOGS
    // =========================================================================
    Route::prefix('audit-logs')
        ->middleware(['auth'])
        ->name('audit-logs.')
        ->group(function () {
            Route::get('/', [AuditLogController::class, 'index'])->name('index');
            Route::get('/data', [AuditLogController::class, 'data'])->name('data');
            Route::get('/stats', [AuditLogController::class, 'stats'])->name('stats');
            Route::get('/export', [AuditLogController::class, 'export'])->name('export');
            Route::delete('/purge', [AuditLogController::class, 'purge'])->name('purge');
            Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
        });

    // =========================================================================
    // 4b. PROFILE MANAGEMENT
    // =========================================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================================
    // 4c. HR MODULE  [module gate: hr]
    // =========================================================================
    Route::prefix('employee-info')->name('employee-info.')->middleware('module:hr')->group(function () {
        Route::get('/create', [HumanResourcesController::class, 'create'])->name('create');
        Route::post('/', [HumanResourcesController::class, 'store'])->name('store');
    });

    // =========================================================================
    // 4d. ADMIN MODULE  [module gate: admin]
    // =========================================================================
    Route::prefix('hr')->name('hr.')->middleware('module:hr')->group(function () {
        // Plantilla
        Route::get('/plantilla', [PlantillaController::class, 'index'])->name('plantilla.index');
        Route::get('/plantilla/create', [PlantillaController::class, 'create'])->name('plantilla.create');
        Route::post('/plantilla', [PlantillaController::class, 'store'])->name('plantilla.store');
        Route::get('/plantilla/{plantilla}', [PlantillaController::class, 'show'])->name('plantilla.show');
        Route::get('/plantilla/{plantilla}/edit', [PlantillaController::class, 'edit'])->name('plantilla.edit');
        Route::put('/plantilla/{plantilla}', [PlantillaController::class, 'update'])->name('plantilla.update');
        Route::delete('/plantilla/{plantilla}', [PlantillaController::class, 'destroy'])->name('plantilla.destroy');
        Route::get('/plantilla/divisions/{officeId}', [PlantillaController::class, 'getDivisions'])->name('plantilla.divisions');
        Route::get('/plantilla/salary/{salaryGradeId}/{step}', [PlantillaController::class, 'getSalary'])->name('plantilla.salary');

        // Salary Grades
        Route::resource('salary-grades', SalaryGradeController::class);

        // Recruitment - Vacancies
        Route::get('/recruitment/vacancies', [RecruitmentController::class, 'vacanciesIndex'])->name('recruitment.vacancies.index');
        Route::get('/recruitment/vacancies/create', [RecruitmentController::class, 'vacanciesCreate'])->name('recruitment.vacancies.create');
        Route::post('/recruitment/vacancies', [RecruitmentController::class, 'vacanciesStore'])->name('recruitment.vacancies.store');
        Route::get('/recruitment/vacancies/{vacancy}', [RecruitmentController::class, 'vacanciesShow'])->name('recruitment.vacancies.show');
        Route::get('/recruitment/vacancies/{vacancy}/edit', [RecruitmentController::class, 'vacanciesEdit'])->name('recruitment.vacancies.edit');
        Route::put('/recruitment/vacancies/{vacancy}', [RecruitmentController::class, 'vacanciesUpdate'])->name('recruitment.vacancies.update');
        Route::delete('/recruitment/vacancies/{vacancy}', [RecruitmentController::class, 'vacanciesDestroy'])->name('recruitment.vacancies.destroy');
        Route::post('/recruitment/vacancies/{vacancy}/publish', [RecruitmentController::class, 'vacanciesPublish'])->name('recruitment.vacancies.publish');
        Route::post('/recruitment/vacancies/{vacancy}/close', [RecruitmentController::class, 'vacanciesClose'])->name('recruitment.vacancies.close');

        // Recruitment - Applicants
        Route::get('/recruitment/applicants', [RecruitmentController::class, 'applicantsIndex'])->name('recruitment.applicants.index');
        Route::get('/recruitment/applicants/create', [RecruitmentController::class, 'applicantsCreate'])->name('recruitment.applicants.create');
        Route::post('/recruitment/applicants', [RecruitmentController::class, 'applicantsStore'])->name('recruitment.applicants.store');
        Route::get('/recruitment/applicants/{applicant}', [RecruitmentController::class, 'applicantsShow'])->name('recruitment.applicants.show');
        Route::get('/recruitment/applicants/{applicant}/edit', [RecruitmentController::class, 'applicantsEdit'])->name('recruitment.applicants.edit');
        Route::put('/recruitment/applicants/{applicant}', [RecruitmentController::class, 'applicantsUpdate'])->name('recruitment.applicants.update');
        Route::delete('/recruitment/applicants/{applicant}', [RecruitmentController::class, 'applicantsDestroy'])->name('recruitment.applicants.destroy');
        Route::post('/recruitment/applicants/{applicant}/select', [RecruitmentController::class, 'applicantsSelect'])->name('recruitment.applicants.select');
        Route::post('/recruitment/applicants/{applicant}/reject', [RecruitmentController::class, 'applicantsReject'])->name('recruitment.applicants.reject');

        // Recruitment - Interviews
        Route::get('/recruitment/interviews', [RecruitmentController::class, 'interviewsIndex'])->name('recruitment.interviews.index');
        Route::get('/recruitment/interviews/schedule', [RecruitmentController::class, 'interviewsSchedule'])->name('recruitment.interviews.schedule');
        Route::post('/recruitment/interviews', [RecruitmentController::class, 'interviewsStore'])->name('recruitment.interviews.store');
        Route::post('/recruitment/interviews/{interview}/result', [RecruitmentController::class, 'interviewsResult'])->name('recruitment.interviews.result');
        Route::delete('/recruitment/interviews/{interview}', [RecruitmentController::class, 'interviewsDestroy'])->name('recruitment.interviews.destroy');

        // Appointments
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
        Route::post('/appointments/{appointment}/terminate', [AppointmentController::class, 'terminate'])->name('appointments.terminate');
        Route::get('/appointments/plantilla/{id}', [AppointmentController::class, 'getPlantillaDetails'])->name('appointments.plantilla-details');
        Route::get('/appointments/applicant/{id}', [AppointmentController::class, 'getApplicantDetails'])->name('appointments.applicant-details');

        // Employee 201 Files
        Route::get('/employees', [Employee201Controller::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [Employee201Controller::class, 'create'])->name('employees.create');
        Route::post('/employees', [Employee201Controller::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}', [Employee201Controller::class, 'show'])->name('employees.show');
        Route::get('/employees/{employee}/edit', [Employee201Controller::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [Employee201Controller::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [Employee201Controller::class, 'destroy'])->name('employees.destroy');
        
        // Employee 201 Details
        Route::post('/employees/{employee}/government-id', [Employee201Controller::class, 'storeGovernmentId'])->name('employees.government-id.store');
        Route::delete('/government-id/{governmentId}', [Employee201Controller::class, 'destroyGovernmentId'])->name('employees.government-id.destroy');
        Route::post('/employees/{employee}/family-background', [Employee201Controller::class, 'storeFamilyBackground'])->name('employees.family-background.store');
        Route::delete('/family-background/{family}', [Employee201Controller::class, 'destroyFamilyBackground'])->name('employees.family-background.destroy');
        Route::post('/employees/{employee}/education', [Employee201Controller::class, 'storeEducation'])->name('employees.education.store');
        Route::delete('/education/{education}', [Employee201Controller::class, 'destroyEducation'])->name('employees.education.destroy');
        Route::post('/employees/{employee}/civil-service', [Employee201Controller::class, 'storeCivilService'])->name('employees.civil-service.store');
        Route::delete('/civil-service/{civilService}', [Employee201Controller::class, 'destroyCivilService'])->name('employees.civil-service.destroy');
        Route::post('/employees/{employee}/work-experience', [Employee201Controller::class, 'storeWorkExperience'])->name('employees.work-experience.store');
        Route::delete('/work-experience/{workExperience}', [Employee201Controller::class, 'destroyWorkExperience'])->name('employees.work-experience.destroy');
        Route::post('/employees/{employee}/document', [Employee201Controller::class, 'storeDocument'])->name('employees.document.store');
        Route::delete('/document/{document}', [Employee201Controller::class, 'destroyDocument'])->name('employees.document.destroy');
        Route::post('/employees/{employee}/training', [Employee201Controller::class, 'storeTraining'])->name('employees.training.store');
        Route::delete('/training/{training}', [Employee201Controller::class, 'destroyTraining'])->name('employees.training.destroy');
    });

    // ==================== ADMIN MODULE ====================
    Route::prefix('admin')->name('admin.')->middleware('module:admin')->group(function () {

        Route::get('/dashboard', AdminDashboard::class)->name('dashboard.index');
        Route::get('/accounts', AccountsManager::class)->name('accounts.index');

        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('index');
            Route::post('/', [DepartmentController::class, 'store'])->name('store');
            Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
            Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('barangays')->name('barangays.')->group(function () {
            Route::get('/', [BarangayController::class, 'index'])->name('index');
            Route::post('/', [BarangayController::class, 'store'])->name('store');
            Route::put('/{barangay}', [BarangayController::class, 'update'])->name('update');
            Route::delete('/{barangay}', [BarangayController::class, 'destroy'])->name('destroy');
        });

        Route::get('/backup-database', [DatabaseBackupController::class, 'backup'])->name('database.backup');

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}', [RoleController::class, 'show'])->name('show');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            Route::post('/{role}/modules', [RoleController::class, 'assignModules'])->name('assign-modules');
        });

        Route::prefix('modules')->name('modules.')->group(function () {
            Route::get('/', [ModuleController::class, 'index'])->name('index');
            Route::post('/', [ModuleController::class, 'store'])->name('store');
            Route::get('/{module}', [ModuleController::class, 'show'])->name('show');
            Route::put('/{module}', [ModuleController::class, 'update'])->name('update');
            Route::delete('/{module}', [ModuleController::class, 'destroy'])->name('destroy');
            Route::post('/{module}/toggle', [ModuleController::class, 'toggle'])->name('toggle');
        });
    });

    // =========================================================================
    // 4e. TREASURY MODULE  [module gate: treasury]
    // =========================================================================
    Route::prefix('treasury')->name('treasury.')->middleware('module:treasury')->group(function () {
        Route::get('/', fn() => view('modules.treasury.index'))->name('index');
        Route::get('/bpls-payment', [BplsPaymentController::class, 'index'])->name('bpls_payment');

        // RPT Payments
        Route::prefix('rpt-payments')->name('rpt.payments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Treasury\RptPaymentController::class, 'index'])->name('index');
            Route::get('/{td}', [\App\Http\Controllers\Treasury\RptPaymentController::class, 'showPaymentForm'])->name('show');
            Route::post('/{billing}/pay', [\App\Http\Controllers\Treasury\RptPaymentController::class, 'storePayment'])->name('store');
            Route::get('/{td}/clearance', [\App\Http\Controllers\Treasury\RptPaymentController::class, 'taxClearance'])->name('clearance');
            Route::get('/{td}/nod', [\App\Http\Controllers\Treasury\RptPaymentController::class, 'generateNOD'])->name('nod');
            Route::get('/payment/{payment}/receipt', [\App\Http\Controllers\Treasury\RptPaymentController::class, 'receipt'])->name('receipt');
        });
    });

    // =========================================================================
    // 4f. OR ASSIGNMENTS  (shared settings, no module gate)
    // =========================================================================
    Route::prefix('settings/or-assignments')->name('or-assignments.')->group(function () {
        Route::get('/', [OrAssignmentController::class, 'index'])->name('index');
        Route::post('/', [OrAssignmentController::class, 'store'])->name('store');
        Route::get('/{orAssignment}/edit', [OrAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{orAssignment}', [OrAssignmentController::class, 'update'])->name('update');
        Route::delete('/{orAssignment}', [OrAssignmentController::class, 'destroy'])->name('destroy');
    });

    // =========================================================================
    // 4g. BPLS MODULE  [module gate: bpls]
    // =========================================================================
    Route::prefix('bpls')->name('bpls.')->middleware('module:bpls')->group(function () {

        // 4g-i. Dashboard
        Route::get('/', [BplsController::class, 'index'])->name('index');
        Route::get('/dashboard/data', [BplsDashboardController::class, 'data'])->name('dashboard.data');

        // 4g-ii. Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [BplsSettingsController::class, 'index'])->name('index');
            Route::post('/update-beneficiary-discount', [BplsSettingsController::class, 'updateBeneficiaryDiscount'])->name('update-beneficiary-discount');
            Route::post('/update-beneficiary-discounts', [BplsSettingsController::class, 'updateBeneficiaryDiscounts'])->name('update-beneficiary-discounts');
            Route::post('/update-general', [BplsSettingsController::class, 'updateGeneral'])->name('update-general');
            Route::post('/update-discount', [BplsSettingsController::class, 'updateDiscount'])->name('update-discount');
            Route::post('/update-permit', [BplsSettingsController::class, 'updatePermit'])->name('update-permit');
            Route::post('/update-receipt', [BplsSettingsController::class, 'updateReceipt'])->name('update-receipt');
            Route::get('/or-assignments', [OrAssignmentController::class, 'index'])->name('or-assignments.index');
            Route::post('/or-assignments', [OrAssignmentController::class, 'store'])->name('or-assignments.store');
            Route::get('/or-assignments/{orAssignment}/edit', [OrAssignmentController::class, 'edit'])->name('or-assignments.edit');
            Route::put('/or-assignments/{orAssignment}', [OrAssignmentController::class, 'update'])->name('or-assignments.update');
            Route::delete('/or-assignments/{orAssignment}', [OrAssignmentController::class, 'destroy'])->name('or-assignments.destroy');
        });

        // 4g-iii. Business Entries
        Route::prefix('business-entries')->name('business-entries.')->group(function () {
            Route::get('/', [BusinessEntriesController::class, 'index'])->name('index');
            Route::post('/', [BusinessEntriesController::class, 'store'])->name('store');
        });

        // 4g-iv. Business List
        Route::prefix('business-list')->name('business-list.')->group(function () {
            Route::get('/', [BusinessListController::class, 'index'])->name('index');
            Route::get('/search', [BusinessListController::class, 'search'])->name('search');
            Route::get('/{entry}', [BusinessListController::class, 'show'])->name('show');
            Route::post('/{entry}/assess', [BusinessListController::class, 'assess'])->name('assess');
            Route::post('/{entry}/approve-payment', [BusinessListController::class, 'approvePayment'])->name('approve-payment');
            Route::post('/{entry}/approve-renewal', [BusinessListController::class, 'approveRenewal'])->name('approve-renewal');
            Route::post('/{entry}/mark-paid', [BusinessListController::class, 'markPaid'])->name('mark-paid');
            Route::post('/{entry}/change-status', [BusinessListController::class, 'changeStatus'])->name('change-status');
            Route::post('/{entry}/retire', [BusinessListController::class, 'retire'])->name('retire');
            Route::get('/{entry}/retirement-certificate', [BusinessListController::class, 'retirementCertificate'])->name('retirement-certificate');
        });

        // 4g-v. Payments
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/{entry}/permit/{payment}', [BplsPaymentController::class, 'permit'])->name('permit');
            Route::get('/{entry}/receipt/{payment}', [BplsPaymentController::class, 'receipt'])->name('receipt');
            Route::post('/{entry}/compute-surcharge', [BplsPaymentController::class, 'computeSurcharge'])->name('compute-surcharge');
            Route::post('/{entry}/validate-or', [BplsPaymentController::class, 'validateOr'])->name('validate-or');
            Route::post('/{entry}/pay', [BplsPaymentController::class, 'pay'])->name('pay');
            Route::post('/{entry}/update-beneficiary', [BplsPaymentController::class, 'updateBeneficiary'])->name('update-beneficiary');
            Route::get('/{entry}', [BplsPaymentController::class, 'show'])->name('show');
        });

        // 4g-vi. Fee Rules
        Route::prefix('fee-rules')->name('fee-rules.')->group(function () {
            Route::get('/manage', fn() => view('modules.bpls.fee-rules.index'))->name('manage');
            Route::post('/reorder', [FeeRuleController::class, 'reorder'])->name('reorder');
            Route::post('/reset-defaults', [FeeRuleController::class, 'resetDefaults'])->name('reset-defaults');
            Route::post('/compute', [FeeRuleController::class, 'compute'])->name('compute');
            Route::post('/compute-online', [FeeRuleController::class, 'computeOnline'])->name('compute-online'); // ← add here
            Route::get('/', [FeeRuleController::class, 'index'])->name('index');
            Route::post('/', [FeeRuleController::class, 'store'])->name('store');
            Route::get('/{feeRule}', [FeeRuleController::class, 'show'])->name('show');
            Route::put('/{feeRule}', [FeeRuleController::class, 'update'])->name('update');
            Route::delete('/{feeRule}', [FeeRuleController::class, 'destroy'])->name('destroy');
            Route::post('/{feeRule}/toggle', [FeeRuleController::class, 'toggle'])->name('toggle');
        });

        // 4g-vii. Form Customization
        Route::prefix('form-customization')->name('form-customization.')->group(function () {
            Route::get('/', [FormCustomizationController::class, 'index'])->name('index');
            Route::post('/', [FormCustomizationController::class, 'store'])->name('store');
            Route::put('/{formOption}', [FormCustomizationController::class, 'update'])->name('update');
            Route::delete('/{formOption}', [FormCustomizationController::class, 'destroy'])->name('destroy');
        });

        // 4g-viii. Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/masterlist', [MasterlistController::class, 'index'])->name('masterlist.index');
            Route::get('/masterlist/data', [MasterlistController::class, 'data'])->name('masterlist.data');
            Route::get('/scale', [BusinessScaleCountController::class, 'index'])->name('scale.index');
            Route::get('/scale/data', [BusinessScaleCountController::class, 'data'])->name('scale.data');
            Route::get('/compliance/quarter', [ComplianceQuarterController::class, 'index'])->name('compliance.quarter.index');
            Route::get('/compliance/quarter/data', [ComplianceQuarterController::class, 'data'])->name('compliance.quarter.data');
            Route::get('/compliance/daterange', [ComplianceDateRangeController::class, 'index'])->name('compliance.daterange.index');
            Route::get('/compliance/daterange/data', [ComplianceDateRangeController::class, 'data'])->name('compliance.daterange.data');
            Route::get('/delinquent', [TaxDelinquentController::class, 'index'])->name('delinquent.index');
            Route::get('/delinquent/data', [TaxDelinquentController::class, 'data'])->name('delinquent.data');
            Route::get('/or-report', [\App\Http\Controllers\Bpls\OrReportController::class, 'index'])->name('or-report.index');
            Route::get('/or-report/print', [\App\Http\Controllers\Bpls\OrReportController::class, 'print'])->name('or-report.print');
            Route::get('/or-report/export', [\App\Http\Controllers\Bpls\OrReportController::class, 'export'])->name('or-report.export');
        });

        // 4g-ix. Online Application Review
        Route::prefix('online')->name('online.')->group(function () {
            Route::get('/application', [BplsApplicationReviewController::class, 'index'])->name('application.index');
            Route::get('/application/{application}', [BplsApplicationReviewController::class, 'show'])->name('application.show');
            Route::post('/documents/{document}/verify', [BplsApplicationReviewController::class, 'verifyDocument'])->name('documents.verify');
            Route::post('/documents/{document}/reject', [BplsApplicationReviewController::class, 'rejectDocument'])->name('documents.reject');
            Route::post('/application/{application}/approve', [BplsApplicationReviewController::class, 'approve'])->name('application.approve');
            Route::post('/application/{application}/return', [BplsApplicationReviewController::class, 'returnToClient'])->name('application.return');
            Route::post('/application/{application}/reject', [BplsApplicationReviewController::class, 'reject'])->name('application.reject');
            Route::post('/application/{application}/assess', [BplsApplicationReviewController::class, 'assess'])->name('application.assess');
            Route::post('/application/{application}/mark-paid', [BplsApplicationReviewController::class, 'markPaid'])->name('application.mark-paid');
            Route::post('/application/{application}/final-approve', [BplsApplicationReviewController::class, 'finalApprove'])->name('application.final-approve');
            Route::post('/application/{application}/confirm-ors', [BplsApplicationReviewController::class, 'confirmOrs'])->name('application.confirm-ors');
        });

        // 4g-x. Permit Signatories
        Route::prefix('permit-signatories')->name('permit-signatories.')->group(function () {
            Route::post('/', [BplsPermitSignatoryController::class, 'store'])->name('store');
            Route::put('/{signatory}', [BplsPermitSignatoryController::class, 'update'])->name('update');
            Route::delete('/{signatory}', [BplsPermitSignatoryController::class, 'destroy'])->name('destroy');
        });

    }); // end bpls module

    // =========================================================================
    // 4h. RPT MODULE  [module gate: rpt]
    // =========================================================================
    Route::prefix('rpt')->name('rpt.')->middleware('module:rpt')->group(function () {

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

        Route::prefix('actual-use')->name('actual-use.')->group(function () {
            Route::get('/', [RptAuController::class, 'index'])->name('index');
            Route::post('/', [RptAuController::class, 'store'])->name('store');
            Route::get('/{rptAu}', [RptAuController::class, 'show'])->name('show');
            Route::post('/{rptAu}', [RptAuController::class, 'update'])->name('update');
            Route::delete('/{rptAu}', [RptAuController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('additional-items')->name('additional-items.')->group(function () {
            Route::get('/', [AdditionalItemController::class, 'index'])->name('index');
            Route::post('/', [AdditionalItemController::class, 'store'])->name('store');
            Route::get('/{additionalItem}', [AdditionalItemController::class, 'show'])->name('show');
            Route::put('/{additionalItem}', [AdditionalItemController::class, 'update'])->name('update');
            Route::delete('/{additionalItem}', [AdditionalItemController::class, 'destroy'])->name('destroy');
        });
    // ==================== BPLS MODULE ====================

    // ==================== RPT MODULE ====================
    Route::prefix('rpt')->name('rpt.')->middleware('module:rpt')->group(function () {

        Route::get('/', [RptDashboardController::class, 'index'])->name('index');

        // Property Registration (Intake)
        Route::prefix('registration')->name('registration.')->group(function () {
            Route::get('/', [PropertyRegistrationController::class, 'index'])->name('index');
            Route::get('/create', [PropertyRegistrationController::class, 'create'])->name('create');
            Route::get('/pending', [PropertyRegistrationController::class, 'pending'])->name('pending');
            Route::post('/', [PropertyRegistrationController::class, 'store'])->name('store');
            Route::get('/{registration}', [PropertyRegistrationController::class, 'show'])->name('show');
            Route::post('/{registration}/archive', [PropertyRegistrationController::class, 'archive'])->name('archive');
        });

        // FAAS — Property Assessment
        Route::prefix('faas')->name('faas.')->group(function () {
            Route::get('/', [FaasPropertyController::class, 'index'])->name('index');
            Route::get('/{registration}/create-draft', [FaasPropertyController::class, 'createDraft'])->name('create-draft');
            Route::post('/{registration}/store-draft', [FaasPropertyController::class, 'storeDraft'])->name('store-draft');
            Route::get('/{registration}/start/{component}', [FaasPropertyController::class, 'startAppraisal'])->name('start');
            Route::post('/bulk-approve', [FaasPropertyController::class, 'bulkApprove'])->name('bulk-approve');
            Route::get('/{faas}', [FaasPropertyController::class, 'show'])->name('show');
            Route::get('/{faas}/compare', [FaasPropertyController::class, 'compare'])->name('compare');
            Route::get('/{faas}/preview-td', [FaasPropertyController::class, 'previewTd'])->name('preview-td');
            Route::get('/{faas}/print-noa', [FaasPropertyController::class, 'printNoa'])->name('print-noa');
            Route::post('/{faas}/submit-review', [FaasPropertyController::class, 'submitReview'])->name('submit-review');
            Route::post('/{faas}/recommend', [FaasPropertyController::class, 'recommendApproval'])->name('recommend');
            Route::post('/{faas}/approve', [FaasPropertyController::class, 'approve'])->name('approve');
            Route::post('/{faas}/return', [FaasPropertyController::class, 'returnToDraft'])->name('return');
            Route::post('/{faas}/revoke-approval', [FaasPropertyController::class, 'revokeApproval'])->name('revoke-approval');
            // Land
            Route::post('/{faas}/land', [FaasPropertyController::class, 'storeLand'])->name('land.store');
            Route::put('/{faas}/land/{land}', [FaasPropertyController::class, 'updateLand'])->name('land.update');
            Route::delete('/{faas}/land/{land}', [FaasPropertyController::class, 'deleteLand'])->name('land.destroy');
            // Building
            Route::post('/{faas}/building', [FaasPropertyController::class, 'storeBuilding'])->name('building.store');
            Route::put('/{faas}/building/{building}', [FaasPropertyController::class, 'updateBuilding'])->name('building.update');
            Route::delete('/{faas}/building/{building}', [FaasPropertyController::class, 'deleteBuilding'])->name('building.destroy');
            // Machinery
            Route::post('/{faas}/machinery', [FaasPropertyController::class, 'storeMachinery'])->name('machinery.store');
            Route::put('/{faas}/machinery/{machinery}', [FaasPropertyController::class, 'updateMachinery'])->name('machinery.update');
            Route::delete('/{faas}/machinery/{machinery}', [FaasPropertyController::class, 'deleteMachinery'])->name('machinery.destroy');
            // Attachments
            Route::post('/{faas}/attachment', [FaasPropertyController::class, 'uploadAttachment'])->name('attachment.store');
            Route::delete('/{faas}/attachment/{attachment}', [FaasPropertyController::class, 'destroyAttachment'])->name('attachment.destroy');
            // General Revision (Governance Check #6)
            Route::post('/{faas}/general-revision', [FaasPropertyController::class, 'generalRevision'])->name('general-revision');
            Route::post('/{faas}/recompute', [FaasPropertyController::class, 'recomputeAll'])->name('recompute');
            Route::post('/{faas}/reassess', [FaasPropertyController::class, 'reassess'])->name('reassess');
            Route::post('/{faas}/transfer', [FaasPropertyController::class, 'transferOwnership'])->name('transfer');
            Route::post('/{faas}/subdivide', [FaasPropertyController::class, 'subdivide'])->name('subdivide');
            Route::post('/consolidate', [FaasPropertyController::class, 'consolidate'])->name('consolidate.store');
            Route::post('/{faas}/cancel', [FaasPropertyController::class, 'cancel'])->name('cancel');
        Route::put('/{faas}/master-update', [FaasPropertyController::class, 'masterUpdate'])->name('master-update');
        });

        // Tax Declarations
        Route::prefix('td')->name('td.')->group(function () {
            Route::post('/bulk-approve', [TaxDeclarationController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-forward', [TaxDeclarationController::class, 'bulkForward'])->name('bulk-forward');
            Route::get('/', [TaxDeclarationController::class, 'index'])->name('index');
            Route::get('/create', [TaxDeclarationController::class, 'create'])->name('create');
            Route::post('/', [TaxDeclarationController::class, 'store'])->name('store');
            Route::get('/{td}', [TaxDeclarationController::class, 'show'])->name('show');
            Route::post('/{td}/submit-review', [TaxDeclarationController::class, 'submitReview'])->name('submit-review');
            Route::post('/{td}/approve', [TaxDeclarationController::class, 'approve'])->name('approve');
            Route::post('/{td}/forward', [TaxDeclarationController::class, 'forwardToTreasury'])->name('forward');
            Route::post('/{td}/cancel', [TaxDeclarationController::class, 'cancel'])->name('cancel');
            Route::get('/{td}/print', [TaxDeclarationController::class, 'print'])->name('print');
            Route::get('/{td}/notice', [TaxDeclarationController::class, 'printNotice'])->name('notice');
        });

        // Online Applications (Staff Review)
        Route::prefix('online-applications')->name('online-applications.')->group(function () {
            Route::get('/', [OnlineApplicationController::class, 'index'])->name('index');
            Route::get('/{application}', [OnlineApplicationController::class, 'show'])->name('show');
            Route::post('/{application}/under-review', [OnlineApplicationController::class, 'markUnderReview'])->name('under-review');
            Route::post('/documents/{document}/verify', [OnlineApplicationController::class, 'verifyDocument'])->name('documents.verify');
            Route::post('/documents/{document}/reject', [OnlineApplicationController::class, 'rejectDocument'])->name('documents.reject');
            Route::post('/{application}/approve', [OnlineApplicationController::class, 'approve'])->name('approve');
            Route::post('/{application}/return', [OnlineApplicationController::class, 'returnToApplicant'])->name('return');
            Route::post('/{application}/reject', [OnlineApplicationController::class, 'reject'])->name('reject');
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
                $ucMethod = ucfirst(lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $slug)))));
                Route::get("/{$slug}", [ReportController::class, $method])->name($name);
                Route::get("/{$slug}/export/pdf", [ReportController::class, 'export' . $ucMethod . 'PDF'])->name("{$name}.export.pdf");
            }
        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [RPTSettingsController::class, 'index'])->name('index');
            Route::post('/classes', [RPTSettingsController::class, 'storeClass'])->name('classes.store');
            Route::put('/classes/{class}', [RPTSettingsController::class, 'updateClass'])->name('classes.update');
            Route::post('/actual-uses', [RPTSettingsController::class, 'storeActualUse'])->name('actual-uses.store');
            Route::put('/actual-uses/{actualUse}', [RPTSettingsController::class, 'updateActualUse'])->name('actual-uses.update');
            
            // Assessment Levels
            Route::get('/assessment-levels', fn() => redirect()->route('rpt.settings.index'));
            Route::post('/assessment-levels', [RPTSettingsController::class, 'storeAssessmentLevel'])->name('assessment-levels.store');
            Route::delete('/assessment-levels/{level}', [RPTSettingsController::class, 'destroyAssessmentLevel'])->name('assessment-levels.destroy');
            
            // Unit Values
            Route::get('/unit-values', fn() => redirect()->route('rpt.settings.index'));
            Route::post('/unit-values', [RPTSettingsController::class, 'storeUnitValue'])->name('unit-values.store');
            Route::delete('/unit-values/{unitValue}', [RPTSettingsController::class, 'destroyUnitValue'])->name('unit-values.destroy');
            
            Route::post('/bldg-types', [RPTSettingsController::class, 'storeBldgType'])->name('bldg-types.store');
            Route::delete('/bldg-types/{bldgType}', [RPTSettingsController::class, 'destroyBldgType'])->name('bldg-types.destroy');
            Route::post('/revision-years', [RPTSettingsController::class, 'storeRevisionYear'])->name('revision-years.store');
            Route::post('/revision-years/{year}/set-current', [RPTSettingsController::class, 'setCurrentRevisionYear'])->name('revision-years.set-current');
            Route::post('/signatories', [RPTSettingsController::class, 'storeSignatory'])->name('signatories.store');
            Route::put('/signatories/{id}', [RPTSettingsController::class, 'updateSignatory'])->name('signatories.update');
            Route::post('/global', [RPTSettingsController::class, 'updateGlobalSettings'])->name('global.update');
            Route::post('/barangay-codes', [RPTSettingsController::class, 'updateBarangayCodes'])->name('barangay-codes.update');
            
            // Fallbacks for other POST routes to avoid MethodNotAllowed on refresh
            Route::get('/classes', fn() => redirect()->route('rpt.settings.index'));
            Route::get('/actual-uses', fn() => redirect()->route('rpt.settings.index'));
            Route::get('/bldg-types', fn() => redirect()->route('rpt.settings.index'));
            Route::get('/revision-years', fn() => redirect()->route('rpt.settings.index'));
            Route::get('/signatories', fn() => redirect()->route('rpt.settings.index'));
        });

    }); // end rpt module

    // =========================================================================
    // 4i. VEHICLE FRANCHISING MODULE  [module gate: vehicle-franchising]  ← NEW
    //     Manages tricycle/transport franchise records, permits, stickers,
    //     OR/CR printing, and franchise reports.
    // =========================================================================
    Route::prefix('vf')->name('vf.')->middleware('module:vehicle-franchising')->group(function () {

        // ---------------------------------------------------------------------
        // 4i-i. Franchise Index / CRUD
        //       Main listing, create, view, edit, renew, delete.
        // ---------------------------------------------------------------------
        Route::get('/', [VehicleFranchisingController::class, 'index'])->name('index');
        Route::get('/create', [VehicleFranchisingController::class, 'create'])->name('create');
        Route::post('/', [VehicleFranchisingController::class, 'store'])->name('store');
        Route::get('/{franchise}', [VehicleFranchisingController::class, 'show'])->name('show');
        Route::get('/{franchise}/edit', [VehicleFranchisingController::class, 'edit'])->name('edit');
        Route::put('/{franchise}', [VehicleFranchisingController::class, 'update'])->name('update');
        Route::delete('/{franchise}', [VehicleFranchisingController::class, 'destroy'])->name('destroy');
        Route::get('/{franchise}/renew', [VehicleFranchisingController::class, 'renew'])->name('renew');
        Route::post('/{franchise}/renew', [VehicleFranchisingController::class, 'storeRenewal'])->name('renew.store');

        // ---------------------------------------------------------------------
        // 4i-ii. Prints
        //        Generate printable Permit, Sticker, and OR/CR documents.
        // ---------------------------------------------------------------------
        Route::prefix('print')->name('print.')->group(function () {
            Route::get('/{franchise}/permit', [VfPrintController::class, 'permit'])->name('permit');
            Route::get('/{franchise}/sticker', [VfPrintController::class, 'sticker'])->name('sticker');
            Route::get('/{franchise}/orcr', [VfPrintController::class, 'orcr'])->name('orcr');
        });

        // ---------------------------------------------------------------------
        // 4i-iii. Reports
        //         Franchise masterlist, TODA summary, yearly collection report.
        // ---------------------------------------------------------------------
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [VfReportController::class, 'index'])->name('index');
            Route::get('/masterlist', [VfReportController::class, 'masterlist'])->name('masterlist');
            Route::get('/masterlist/data', [VfReportController::class, 'masterlistData'])->name('masterlist.data');
            Route::get('/toda-summary', [VfReportController::class, 'todaSummary'])->name('toda-summary');
            Route::get('/collection', [VfReportController::class, 'collection'])->name('collection');
        });

    }); // end vehicle-franchising module

}); // end auth + verified middleware group

// =============================================================================
// 5. CLIENT PORTAL  [guard: client]
// =============================================================================
Route::prefix('portal')->name('client.')->group(function () {

    Route::middleware('guest:client')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware([\App\Http\Middleware\ClientAuthenticated::class])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('applications')->name('applications.')->group(function () {
            Route::get('/', [ApplicationController::class, 'index'])->name('index');
            Route::get('/create', [ApplicationController::class, 'create'])->name('create');
            Route::post('/', [ApplicationController::class, 'store'])->name('store');
            Route::get('/{application}', [ApplicationController::class, 'show'])->name('show');
            Route::get('/{application}/edit', [ApplicationController::class, 'edit'])->name('edit');
            Route::put('/{application}', [ApplicationController::class, 'update'])->name('update');
            Route::get('/{application}/renew', [ApplicationController::class, 'renew'])->name('renew');
            Route::get('/{application}/permit/download', [ApplicationController::class, 'downloadPermit'])->name('permit.download');
            Route::delete('/{application}', [ApplicationController::class, 'destroy'])->name('destroy');
        });

        Route::get('/apply', [ApplicationController::class, 'create'])->name('apply');
        Route::post('/apply', [ApplicationController::class, 'store'])->name('apply.store');

        Route::prefix('applications/{application}/documents')->name('documents.')->group(function () {
            Route::get('/', [DocumentUploadController::class, 'index'])->name('index');
            Route::post('/', [DocumentUploadController::class, 'upload'])->name('upload');
            Route::delete('/{document}', [DocumentUploadController::class, 'destroy'])->name('destroy');
            Route::post('/submit', [DocumentUploadController::class, 'submit'])->name('submit');
        });

        Route::prefix('applications/{application}/payment')->name('payment.')->group(function () {
            Route::get('/', [PaymentController::class, 'show'])->name('show');
            Route::post('/', [PaymentController::class, 'initiate'])->name('initiate');
            Route::post('/confirm', [PaymentController::class, 'confirm'])->name('confirm');
            Route::get('/success', [PaymentController::class, 'success'])->name('success');
            Route::get('/receipt/{payment}', [PaymentController::class, 'receipt'])->name('receipt');
        });

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/walkin-payments', [WalkInPaymentsController::class, 'index'])->name('walkin-payments');
        Route::get('/walkin-payments/{payment}/receipt', [WalkInPaymentsController::class, 'receipt'])->name('walkin-payments.receipt');
        Route::get('/walkin-payments/{payment}/permit', [WalkInPaymentsController::class, 'permit'])->name('walkin-payments.permit');

        // RPT Online Property Registration
        Route::prefix('rpt')->name('rpt.')->group(function () {
            Route::get('/', [RptApplicationController::class, 'index'])->name('index');
            Route::get('/apply', [RptApplicationController::class, 'create'])->name('create');
            Route::post('/apply', [RptApplicationController::class, 'store'])->name('store');
            Route::get('/{application}', [RptApplicationController::class, 'show'])->name('show');
        });

        // Client search (reusing BPLS controllers but under client auth)
        Route::get('/search-owner', [BusinessEntriesController::class, 'searchOwner'])->name('search-owner');
        Route::get('/search-business', [BusinessEntriesController::class, 'searchBusiness'])->name('search-business');
    });

}); // end client portal

// =============================================================================
// 6. PUBLIC WEBHOOKS
// =============================================================================
Route::post('/portal/webhook/paymongo', [PaymentController::class, 'webhook'])
    ->name('client.webhook.paymongo')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// =============================================================================
// 7. LGU/STAFF AUTH ROUTES
// =============================================================================
require __DIR__ . '/auth.php';