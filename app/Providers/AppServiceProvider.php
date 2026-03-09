<?php
// app/Providers/AppServiceProvider.php
//
// Add the observer registrations to your boot() method.
// Also shows how to integrate AuditLog::record() inside the BPLS controllers.

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Models to observe
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\OrAssignment;
use Illuminate\Pagination\Paginator;

// Observer
use App\Observers\AuditLogObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── Register the generic Eloquent observer ──────────────────────────
        // Every create / update / delete on these models is automatically
        // written to audit_logs with old + new values.

        BusinessEntry::observe(AuditLogObserver::class);
        BplsPayment::observe(AuditLogObserver::class);
        OrAssignment::observe(AuditLogObserver::class);

        Paginator::useTailwind();

        // Add any other models you want tracked, e.g.:
        // \App\Models\BplsSetting::observe(AuditLogObserver::class);
    }
}

// ============================================================================
// MANUAL LOGGING EXAMPLES
//
// Use AuditLog::record() for actions that are NOT simple CRUD
// (payments, status changes, logins, exports, …).
//
// Paste the relevant snippet into the matching controller method.
// ============================================================================

/*
 * ── BplsPaymentController@pay ──────────────────────────────────────────────
 * After $payment = BplsPayment::create([...]):
 *
 *   \App\Models\AuditLog::record(
 *       'BPLS',
 *       'payment',
 *       "Payment O.R. #{$payment->or_number} recorded for {$entry->business_name}. Total: ₱" . number_format($total, 2),
 *       $entry,
 *       [
 *           'extra' => [
 *               'or_number'      => $payment->or_number,
 *               'quarters_paid'  => $quarters,
 *               'total_collected'=> $total,
 *               'payment_method' => $request->payment_method,
 *               'discount'       => $discount,
 *               'surcharges'     => $surcharges,
 *           ],
 *       ]
 *   );
 *
 * ── BplsPaymentController@approvePayment ───────────────────────────────────
 * After $entry->update([...]):
 *
 *   \App\Models\AuditLog::record(
 *       'BPLS',
 *       'status_change',
 *       "Business '{$entry->business_name}' approved for payment. Total due: ₱" . number_format($request->total_due, 2),
 *       $entry,
 *       [
 *           'old_values' => ['status' => $entry->getOriginal('status')],
 *           'new_values' => ['status' => 'for_payment', 'total_due' => $request->total_due],
 *       ]
 *   );
 *
 * ── BplsPaymentController@approveRenewal ───────────────────────────────────
 * After $entry->update([...]):
 *
 *   \App\Models\AuditLog::record(
 *       'BPLS',
 *       'status_change',
 *       "Renewal approved for '{$entry->business_name}'. Cycle #{$newCycle}, Year {$newPermitYear}.",
 *       $entry,
 *       [
 *           'new_values' => [
 *               'renewal_cycle' => $newCycle,
 *               'permit_year'   => $newPermitYear,
 *               'total_due'     => $request->total_due,
 *           ],
 *       ]
 *   );
 *
 * ── BusinessEntriesController@updateStatus ─────────────────────────────────
 * After $businessEntry->update(['status' => $newStatus]):
 *
 *   \App\Models\AuditLog::record(
 *       'BPLS',
 *       'status_change',
 *       "Status of '{$businessEntry->business_name}' changed to '{$newStatus}'.",
 *       $businessEntry,
 *       [
 *           'old_values' => ['status' => $businessEntry->getOriginal('status')],
 *           'new_values' => ['status' => $newStatus],
 *       ]
 *   );
 *
 * ── OrAssignmentController@store ───────────────────────────────────────────
 * After OrAssignment::create([...]):
 *
 *   \App\Models\AuditLog::record(
 *       'OR Assignment',
 *       'created',
 *       "OR range {$request->start_or}–{$request->end_or} ({$request->receipt_type}) assigned to {$cashierName}.",
 *   );
 *
 * ── Auth (LoginController / AuthenticatedSessionController) ────────────────
 * On successful login:
 *
 *   \App\Models\AuditLog::record('Auth', 'login', 'User logged in.');
 *
 * On logout:
 *
 *   \App\Models\AuditLog::record('Auth', 'logout', 'User logged out.');
 *
 * ── MasterlistController@export ────────────────────────────────────────────
 * Before returning the download response:
 *
 *   \App\Models\AuditLog::record('BPLS', 'export', 'Masterlist exported to CSV.');
 *
 */