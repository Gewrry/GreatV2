<?php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;
use App\Models\RPT\RptTcTbl;
use App\Models\RPT\FaasRptaAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RptTransactionCodeController extends Controller
{
    /**
     * Display transaction codes management page
     */
    public function index()
    {
        $transactionCodes = RptTcTbl::orderBy('tcode')->get();
        return view('modules.rpt.rpta_settings.transaction_code', compact('transactionCodes'));
    }

    /**
     * Store a new transaction code
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tcode' => 'required|string|max:5|unique:rpt_tc_tbl,tcode',
            'tcode_desc' => 'required|string|max:200',
        ]);

        // Create audit log entry
        $this->createAuditLog(
            Auth::user()->name ?? 'system',
            'create',
            [
                'SECTION' => 'Transaction Code for RPU',
                'TRANSACTION CODE' => $validated['tcode'],
                'DESC' => $validated['tcode_desc']
            ],
            null
        );

        $transactionCode = RptTcTbl::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaction Code has been added successfully.',
            'data' => $transactionCode
        ]);
    }

    /**
     * Show a transaction code (for AJAX/JSON)
     */
    public function show(RptTcTbl $transaction_code)
    {
        return response()->json($transaction_code);
    }

    /**
     * Update a transaction code
     */
    public function update(Request $request, RptTcTbl $transaction_code)
    {
        $validated = $request->validate([
            'tcode' => 'required|string|max:5|unique:rpt_tc_tbl,tcode,' . $transaction_code->id,
            'tcode_desc' => 'required|string|max:200',
        ]);

        // Store old data for audit
        $oldData = [
            'tcode' => $transaction_code->tcode,
            'tcode_desc' => $transaction_code->tcode_desc
        ];

        // Create audit log entry
        $this->createAuditLog(
            Auth::user()->name ?? 'system',
            'update',
            [
                'SECTION' => 'Transaction Code for RPU',
                'TRANSACTION CODE' => $validated['tcode'],
                'DESC' => $validated['tcode_desc']
            ],
            [
                'SECTION' => 'Transaction Code for RPU',
                'TRANSACTION CODE' => $oldData['tcode'],
                'DESC' => $oldData['tcode_desc']
            ]
        );

        $transaction_code->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaction Code has been updated successfully.',
            'data' => $transaction_code
        ]);
    }

    /**
     * Delete a transaction code
     */
    public function destroy(RptTcTbl $transaction_code)
    {
        // Create audit log entry for deletion
        $this->createAuditLog(
            Auth::user()->name ?? 'system',
            'delete',
            null,
            [
                'SECTION' => 'Transaction Code for RPU',
                'TRANSACTION CODE' => $transaction_code->tcode,
                'DESC' => $transaction_code->tcode_desc
            ]
        );

        $transaction_code->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction Code has been deleted successfully.'
        ]);
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