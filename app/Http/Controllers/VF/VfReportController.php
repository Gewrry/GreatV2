<?php
// app/Http/Controllers/VF/VfReportController.php

namespace App\Http\Controllers\VF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VfReportController extends Controller
{
    public function index()
    {
        return view('modules.vf.reports.index');
    }

    public function masterlist(Request $request)
    {
        return view('modules.vf.reports.masterlist');
    }

    public function masterlistData(Request $request)
    {
        // TODO: return paginated/filtered JSON data for DataTables
        return response()->json(['data' => []]);
    }

    public function todaSummary(Request $request)
    {
        return view('modules.vf.reports.toda-summary');
    }

    public function collection(Request $request)
    {
        return view('modules.vf.reports.collection');
    }
}