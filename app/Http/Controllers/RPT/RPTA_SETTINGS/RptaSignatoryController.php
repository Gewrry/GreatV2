<?php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;
use App\Models\RPT\RptaSignatory;
use App\Models\RPT\RptaRevYr;
use App\Models\RPT\Defaultz;
use App\Models\RPT\FaasRptaAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RptaSignatoryController extends Controller
{
    /**
     * Display the signatories management page
     */
    public function index()
    {
        $signatories = RptaSignatory::orderBy('created_at', 'desc')->get();
        $revYear = RptaRevYr::firstOrCreate(['id' => 1], ['rev_yr' => date('Y')]);
        $defaultSignatories = Defaultz::where('id', '!=', 1)->get();

        return view('modules.rpt.rpta_settings.signatories', compact('signatories', 'revYear', 'defaultSignatories'));
    }

    /**
     * Store a newly created signatory
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sign_name' => 'required|string|max:255',
            'sign_name_ext' => 'required|string|max:255',
            'sign_assign' => 'required|date',
        ]);

        // Create audit log entry
        $this->createAuditLog(
            Auth::user()->name ?? 'system',
            'create',
            [
                'SECTION' => 'Addition of Signatory',
                'SIGNATORY NAME' => $validated['sign_name'],
                'DESIGNATION' => $validated['sign_name_ext'],
                'DATE ASSIGN' => $validated['sign_assign']
            ],
            null
        );

        RptaSignatory::create($validated);

        return redirect()->route('rpt.signatories.index')
            ->with('success', 'Signatory has been added successfully.');
    }

    /**
     * Display the specified signatory (for AJAX/JSON response)
     */
    public function show(RptaSignatory $signatory)
    {
        return response()->json([
            'id' => $signatory->id,
            'sign_name' => $signatory->sign_name,
            'sign_name_ext' => $signatory->sign_name_ext,
            'sign_assign' => $signatory->sign_assign->format('Y-m-d')
        ]);
    }

    /**
     * Update the specified signatory
     */
    public function update(Request $request, RptaSignatory $signatory)
    {
        $validated = $request->validate([
            'sign_name' => 'required|string|max:255',
            'sign_name_ext' => 'required|string|max:255',
            'sign_assign' => 'required|date',
        ]);

        // Create audit log entry
        $this->createAuditLog(
            Auth::user()->name ?? 'system',
            'update',
            [
                'SECTION' => 'Update Signatory',
                'SIGNATORY NAME' => $validated['sign_name'],
                'DESIGNATION' => $validated['sign_name_ext'],
                'DATE ASSIGN' => $validated['sign_assign']
            ],
            [
                'SECTION' => 'Previous Signatory',
                'SIGNATORY NAME' => $signatory->sign_name,
                'DESIGNATION' => $signatory->sign_name_ext,
                'DATE ASSIGN' => $signatory->sign_assign->format('Y-m-d')
            ]
        );

        $signatory->update($validated);

        return redirect()->route('rpt.signatories.index')
            ->with('success', 'Signatory has been updated successfully.');
    }

    /**
     * Update revision year
     */
    public function updateRevisionYear(Request $request)
    {
        $validated = $request->validate([
            'rev_yr' => 'required|integer|min:2000|max:' . (date('Y') + 10),
        ]);

        $oldYear = RptaRevYr::firstOrCreate(['id' => 1], ['rev_yr' => date('Y')]);

        // Create audit log entry
        $this->createAuditLog(
            Auth::user()->name ?? 'system',
            'update',
            [
                'SECTION' => 'TO Revision Year',
                'REVISION YEAR' => $validated['rev_yr']
            ],
            [
                'SECTION' => 'FROM Revision Year',
                'REVISION YEAR' => $oldYear->rev_yr
            ]
        );

        $oldYear->update(['rev_yr' => $validated['rev_yr']]);

        return redirect()->route('rpt.signatories.index')
            ->with('success', 'Revision Year has been updated successfully.');
    }

    /**
     * Update report/certificate signatories
     */
    public function updateRcSignatory(Request $request)
    {
        $validated = $request->validate([
            'rc_type' => 'required|in:2,3',
            'mun_assessor' => 'required|string|max:255',
            'mun_ass_designation' => 'required|string|max:255',
        ]);

        $oldData = Defaultz::findOrFail($validated['rc_type']);

        // Create audit log entry
        $this->createAuditLog(
            Auth::user()->name ?? 'system',
            'update',
            [
                'SECTION' => 'Report/Certificate Signatory',
                'TYPE' => $validated['rc_type'] == 2 ? 'Municipal/City Assessor' : 'Provincial Assessor',
                'SIGNATORY' => $validated['mun_assessor'],
                'DESIGNATION' => $validated['mun_ass_designation']
            ],
            [
                'SECTION' => 'Report/Certificate Signatory',
                'TYPE' => $validated['rc_type'] == 2 ? 'Municipal/City Assessor' : 'Provincial Assessor',
                'SIGNATORY' => $oldData->mun_assessor,
                'DESIGNATION' => $oldData->mun_ass_designation
            ]
        );

        $oldData->update([
            'mun_assessor' => $validated['mun_assessor'],
            'mun_ass_designation' => $validated['mun_ass_designation']
        ]);

        return redirect()->route('rpt.signatories.index')
            ->with('success', 'Report/Certificate Signatory has been updated successfully.');
    }

    /**
     * Remove the specified signatory
     */
    public function destroy(RptaSignatory $signatory)
    {
        // Create audit log entry for deletion
        $this->createAuditLog(
            Auth::user()->name ?? 'system',
            'delete',
            null,
            [
                'SECTION' => 'Deletion of Signatory',
                'SIGNATORY NAME' => $signatory->sign_name,
                'DESIGNATION' => $signatory->sign_name_ext,
                'DATE ASSIGN' => $signatory->sign_assign->format('Y-m-d')
            ]
        );

        $signatory->delete();

        return redirect()->route('rpt.signatories.index')
            ->with('success', 'Signatory has been deleted successfully.');
    }

    /**
     * Helper method to create audit logs
     */
    private function createAuditLog($username, $action, $newData, $oldData)
    {
        FaasRptaAudit::create([
            'username' => $username,
            'action_taken' => $action,
            'new_data' => $newData,
            'old_data' => $oldData
        ]);
    }
}