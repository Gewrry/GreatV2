<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\FaasProperty;
use App\Models\RPT\FaasLand;
use App\Models\RPT\FaasBuilding;
use App\Models\RPT\FaasMachinery;
use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\TdActivityLog;
use App\Models\RPT\FaasActivityLog;
use App\Models\RPT\RptaRevisionYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RPT\StoreTdRequest;
use App\Services\RPT\TdValidationService;

class TaxDeclarationController extends Controller
{
    // ─── LISTING ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $tds = TaxDeclaration::with(['property.barangay', 'revisionYear'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('td_no',     'like', "%{$search}%")
                        ->orWhereHas('property.owners', fn($q2) =>
                            $q2->where('owner_name', 'like', "%{$search}%")
                        )
                        ->orWhereHas('property', fn($q2) =>
                            $q2->where('arp_no', 'like', "%{$search}%")
                        );
                });
            })
            ->when($request->filled('status'),        fn($q) => $q->where('status', $request->status))
            ->when($request->filled('property_type'), fn($q) => $q->where('property_type', $request->property_type))
            ->when($request->filled('barangay_id'),   fn($q) => $q->whereHas('property', fn($q2) =>
                $q2->where('barangay_id', $request->barangay_id)
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $barangays      = \App\Models\Barangay::orderBy('brgy_name')->get();
        $forReviewCount = TaxDeclaration::where('status', 'for_review')->count();
        $approvedCount  = TaxDeclaration::where('status', 'approved')->count();
        $forwardedCount = TaxDeclaration::where('status', 'forwarded')->count();

        return view('modules.rpt.td.index', compact(
            'tds', 'barangays',
            'forReviewCount', 'approvedCount', 'forwardedCount'
        ));
    }
    public function create(Request $request)
    {
        $property = $request->filled('faas_property_id')
            ? FaasProperty::with(['lands.actualUse', 'buildings.actualUse', 'machineries.actualUse'])->findOrFail($request->faas_property_id)
            : null;

        // Only approved FAAS records can have a TD created
        if ($property && !$property->isApproved()) {
            return redirect()->back()->with('error', 'Tax Declarations can only be created for APPROVED property records.');
        }

        // Pre-select a specific component if passed via query string (from Generate TD button)
        $preComponentType = $request->component_type; // 'land' | 'building' | 'machinery'
        $preComponentId   = $request->component_id;

        $approvedFaas  = FaasProperty::approved()->get(['id', 'arp_no']);
        $revisionYears = RptaRevisionYear::orderByDesc('year')->get();

        return view('modules.rpt.td.create', compact(
            'property', 'revisionYears', 'approvedFaas',
            'preComponentType', 'preComponentId'
        ));
    }

    // ─── STORE ──────────────────────────────────────────────────────────────────

    public function store(StoreTdRequest $request, TdValidationService $validator)
    {
        $property = FaasProperty::with(['lands', 'buildings', 'machineries'])->findOrFail($request->faas_property_id);

        // Resolve the specific component and extract its individual assessed value
        $componentType = $request->component_type;
        $componentId   = (int) $request->component_id;

        [$component, $fkField] = match($componentType) {
            'land'      => [FaasLand::findOrFail($componentId),      'faas_land_id'],
            'building'  => [FaasBuilding::findOrFail($componentId),  'faas_building_id'],
            'machinery' => [FaasMachinery::findOrFail($componentId), 'faas_machinery_id'],
        };

        // Security: ensure the component belongs to THIS FAAS
        abort_if($component->faas_property_id !== $property->id, 403,
            'Integrity Error: The selected component does not belong to this FAAS record.');

        // Duplicate guard: a component should not have two active TDs
        $validator->assertCanStore($property, (int) $request->effectivity_year);
        $existingTd = TaxDeclaration::where($fkField, $componentId)
            ->whereNotIn('status', ['cancelled'])
            ->first();
        if ($existingTd) {
            return redirect()->back()->with('error',
                'This ' . ucfirst($componentType) . ' already has an active Tax Declaration (TD No: ' . ($existingTd->td_no ?? 'Draft') . '. Cancel the existing TD before creating a new one.');
        }

        $td = DB::transaction(function () use ($request, $property, $component, $componentType, $fkField) {
            $td = TaxDeclaration::create([
                'faas_property_id'     => $property->id,
                $fkField               => $component->id,
                'property_kind'        => $componentType,
                // Derive property_type from component kind for backwards compat
                'property_type'        => $componentType,
                'effectivity_year'     => $request->effectivity_year,
                'effectivity_quarter'  => $request->effectivity_quarter,
                'revision_year_id'     => $request->revision_year_id,
                'declaration_reason'   => $request->declaration_reason,
                'tax_rate'             => $request->tax_rate,
                'is_taxable'           => $request->boolean('is_taxable', true),
                'exemption_basis'      => $request->exemption_basis,
                'cancelled_td_no'      => $request->cancelled_td_no,
                'cancellation_reason'  => $request->cancellation_reason,
                'remarks'              => $request->remarks,
                // MRPAAO-Compliant: use only THIS component's values
                'total_market_value'   => $component->market_value,
                'total_assessed_value' => $component->assessed_value,
                'status'               => 'draft',
                'created_by'           => Auth::id(),
            ]);

            TdActivityLog::record($td->id, 'created', 'Tax Declaration created for ' . ucfirst($componentType) . '.', [
                'created_by'           => Auth::id(),
                'component_type'       => $componentType,
                'component_id'         => $component->id,
                'total_assessed_value' => $td->total_assessed_value,
                'tax_rate'             => $td->tax_rate,
                'effectivity_year'     => $td->effectivity_year,
            ]);

            // Quick Approve Logic
            if ($request->boolean('auto_approve')) {
                $basicTaxDue = $td->annualTaxDue();
                $td->update([
                    'status'                       => 'approved',
                    'td_no'                        => TaxDeclaration::generateTdNo(),
                    'approved_by'                  => Auth::id(),
                    'approved_at'                  => now(),
                    'total_market_value'           => $td->total_market_value,
                    'total_assessed_value'         => $td->total_assessed_value,
                    'tax_rate'                     => $td->tax_rate,
                    'basic_tax_snapshot'           => $basicTaxDue,
                ]);
                TdActivityLog::record($td->id, 'approved', 'TD Quick-Approved. TD No. ' . $td->td_no . ' assigned.', [
                    'approved_by'       => Auth::id(),
                    'td_no'             => $td->td_no,
                    'basic_tax_snapshot'=> $basicTaxDue,
                    'quick_approved'    => true,
                ]);
            }

            return $td;
        });

        $msg = $request->boolean('auto_approve') ? 'Tax Declaration created and approved successfully.' : 'Tax Declaration created.';
        return redirect()->route('rpt.td.show', $td)->with('success', $msg);
    }

    // ─── SHOW ────────────────────────────────────────────────────────────────────

    public function show(TaxDeclaration $td, Request $request)
    {
        $td->load([
            'property.lands.actualUse',
            'property.buildings.actualUse',
            'property.machineries.actualUse',
            'billings.payments',
            'createdBy',
            'approvedBy',
            'activityLogs.user',
        ]);

        if ($request->ajax() || $request->headers->get('X-Requested-With') === 'XMLHttpRequest') {
            return view('modules.rpt.td.partials._details', compact('td'))->with('isModal', true);
        }

        return view('modules.rpt.td.show', compact('td'));
    }

    // ─── WORKFLOW ────────────────────────────────────────────────────────────────

    public function submitReview(TaxDeclaration $td)
    {
        abort_if($td->status !== 'draft', 403, 'Only Draft TDs can be submitted for review.');

        $td->update(['status' => 'for_review']);

        TdActivityLog::record($td->id, 'submitted_review', 'Submitted for review by ' . Auth::user()->name . '.');
        return back()->with('success', 'TD submitted for review.');
    }

    public function approve(TaxDeclaration $td, TdValidationService $validator)
    {
        $validator->assertCanApprove($td);

        DB::transaction(function () use ($td) {
            $basicTaxDue = $td->annualTaxDue();

            $td->update([
                'status'                        => 'approved',
                'td_no'                         => TaxDeclaration::generateTdNo(),
                'approved_by'                   => Auth::id(),
                'approved_at'                   => now(),
                // Hardened Financial Snapshots
                'total_market_value'             => $td->total_market_value,
                'total_assessed_value'           => $td->total_assessed_value,
                'tax_rate'                       => $td->tax_rate,
                'basic_tax_snapshot'             => $basicTaxDue,
            ]);

            // Governance Check #5: Log WHO approved and snapshot values
            TdActivityLog::record($td->id, 'approved', 'Approved by ' . Auth::user()->name . '. TD No. ' . $td->td_no . ' assigned.', [
                'approved_by'          => Auth::id(),
                'td_no'                => $td->td_no,
                'assessed_value_snapshot' => $td->total_assessed_value,
                'basic_tax_snapshot'   => $basicTaxDue,
            ]);
        });

        return back()->with('success', 'TD approved. TD No.: ' . $td->td_no);
    }

    public function forwardToTreasury(TaxDeclaration $td, TdValidationService $validator)
    {
        $validator->assertCanForward($td);

        $td->update(['status' => 'forwarded']);

        // Governance Check #5: Log who forwarded
        TdActivityLog::record($td->id, 'forwarded', 'Forwarded to Treasury by ' . Auth::user()->name . '.', [
            'forwarded_by' => Auth::id(),
            'forwarded_at' => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'TD forwarded to Treasury. It is now locked for Assessor edits.');
    }

    public function cancel(Request $request, TaxDeclaration $td)
    {
        // Governance: Forwarded TDs cannot be cancelled by the Assessor — requires Treasury clearance
        abort_if($td->status === 'forwarded', 403, 'A forwarded TD cannot be cancelled. Coordinate with the Treasury Office.');
        abort_if($td->status === 'cancelled', 422, 'TD is already cancelled.');

        $request->validate(['remarks' => 'nullable|string|max:1000']);

        $td->update(['status' => 'cancelled', 'remarks' => $request->remarks]);

        TdActivityLog::record($td->id, 'cancelled', 'Cancelled by ' . Auth::user()->name . '. Reason: ' . ($request->remarks ?? 'None.'));
        return back()->with('success', 'TD cancelled.');
    }

    public function print(TaxDeclaration $td)
    {
        // Governance: Only approved or forwarded TDs can be printed
        abort_if(!in_array($td->status, ['approved', 'forwarded']), 403, 'Only approved TDs can be printed.');

        $td->load(['property.barangay', 'property.lands.actualUse', 'property.buildings', 'property.machineries', 'revisionYear', 'approvedBy']);
        return view('modules.rpt.td.print', compact('td'));
    }

    /**
     * Bulk Approve Tax Declarations.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tax_declarations,id',
        ]);

        $count = DB::transaction(function () use ($request) {
            $approved = 0;
            $items = TaxDeclaration::whereIn('id', $request->ids)
                ->where('status', 'for_review')
                ->get();

            foreach ($items as $item) {
                // Use reuse existing approve logic behavior
                $basicTaxDue = $item->annualTaxDue();
                $item->update([
                    'status'                        => 'approved',
                    'td_no'                         => TaxDeclaration::generateTdNo(),
                    'approved_by'                   => Auth::id(),
                    'approved_at'                   => now(),
                    'total_market_value'            => $item->total_market_value,
                    'total_assessed_value'          => $item->total_assessed_value,
                    'tax_rate'                      => $item->tax_rate,
                    'basic_tax_snapshot'            => $basicTaxDue,
                ]);

                TdActivityLog::record($item->id, 'approved', 'Approved via bulk operation by ' . Auth::user()->name . '. TD No. ' . $item->td_no . ' assigned.');
                $approved++;
            }
            return $approved;
        });

        return back()->with('success', "{$count} Tax Declarations approved successfully.");
    }

    /**
     * Bulk Forward Tax Declarations to Treasury.
     */
    public function bulkForward(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tax_declarations,id',
        ]);

        $count = DB::transaction(function () use ($request) {
            $forwarded = 0;
            $items = TaxDeclaration::whereIn('id', $request->ids)
                ->where('status', 'approved')
                ->get();

            foreach ($items as $item) {
                $item->update(['status' => 'forwarded']);
                TdActivityLog::record($item->id, 'forwarded', 'Forwarded to Treasury via bulk operation by ' . Auth::user()->name . '.');
                $forwarded++;
            }
            return $forwarded;
        });

        return back()->with('success', "{$count} Tax Declarations forwarded to Treasury successfully.");
    }

    // ─── NOTICE OF ASSESSMENT (Sec. 223, RA 7160) ──────────────────────────────

    /**
     * Print the formal Notice of Assessment for a Tax Declaration.
     * Required by the Local Government Code for every new/revised assessment.
     */
    public function printNotice(TaxDeclaration $td)
    {
        $td->load(['property.barangay', 'property.previousFaas', 'land', 'building', 'machinery', 'revisionYear']);

        return view('modules.rpt.td.print.notice', compact('td'));
    }
}
