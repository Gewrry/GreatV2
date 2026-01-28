<?php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\EmployeeInfo;

class RPTA_SettingsController extends Controller
{
    public function index()
    {
        return view('modules.rpt.rpta_settings.index');
    }
}
