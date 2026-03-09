<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\RptaClass;
use App\Models\RPT\RptaActualUse;
use App\Models\RPT\RptaAssessmentLevel;
use App\Models\RPT\RptaUnitValue;
use App\Models\RPT\RptaBldgType;
use App\Models\RPT\RptaRevisionYear;
use App\Models\RPT\RptaSignatory;
use App\Models\RPT\RptaSetting;
use App\Models\Barangay;
use Illuminate\Http\Request;

class RPTSettingsController extends Controller
{
    public function index()
    {
        $currentRevision = RptaRevisionYear::current();
        
        $classes       = RptaClass::with('actualUses')->get();
        $bldgTypes     = RptaBldgType::all();
        $revisionYears = RptaRevisionYear::orderByDesc('year')->get();
        $signatories   = \DB::table('rpta_signatories')->get();
        $barangays     = Barangay::orderBy('brgy_name')->get();
        $settings      = RptaSetting::pluck('setting_value', 'setting_key')->toArray();

        // New data for the expanded settings
        $assessmentLevels = RptaAssessmentLevel::with('actualUse')
            ->when($currentRevision, fn($q) => $q->where('revision_year_id', $currentRevision->id))
            ->get();

        $unitValues = RptaUnitValue::with(['actualUse', 'barangay'])
            ->when($currentRevision, fn($q) => $q->where('revision_year_id', $currentRevision->id))
            ->get();

        return view('modules.rpt.settings.index', compact(
            'classes', 'bldgTypes', 'revisionYears', 'signatories', 
            'barangays', 'settings', 'currentRevision', 
            'assessmentLevels', 'unitValues'
        ));
    }

    public function updateGlobalSettings(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            RptaSetting::set($key, $value);
        }
        return back()->with('success', 'Global settings updated.');
    }

    public function updateBarangayCodes(Request $request)
    {
        $request->validate([
            'barangay_id'   => 'required|exists:barangays,id',
            'brgy_district' => 'nullable|string|max:10',
            'brgy_code'     => 'nullable|string|max:10',
        ]);

        $brgy = Barangay::find($request->barangay_id);
        $brgy->update([
            'brgy_district' => $request->brgy_district,
            'brgy_code'     => $request->brgy_code,
        ]);

        return back()->with('success', 'Barangay codes updated for ' . $brgy->brgy_name);
    }

    // ─── Classifications ─────────────────────────────────────────────────────────

    public function storeClass(Request $request)
    {
        $request->validate(['name' => 'required|string', 'code' => 'required|string|max:10|unique:rpta_classes,code']);
        RptaClass::create($request->only('name', 'code'));
        return back()->with('success', 'Classification added.');
    }

    public function updateClass(Request $request, RptaClass $class)
    {
        $class->update($request->only('name', 'is_active'));
        return back()->with('success', 'Classification updated.');
    }

    // ─── Actual Uses ─────────────────────────────────────────────────────────────

    public function storeActualUse(Request $request)
    {
        $request->validate([
            'rpta_class_id' => 'required|exists:rpta_classes,id',
            'name'          => 'required|string',
            'code'          => 'required|string|max:20|unique:rpta_actual_uses,code',
        ]);
        RptaActualUse::create($request->only('rpta_class_id', 'name', 'code'));
        return back()->with('success', 'Actual use added.');
    }

    public function updateActualUse(Request $request, RptaActualUse $actualUse)
    {
        $actualUse->update($request->only('name', 'is_active'));
        return back()->with('success', 'Actual use updated.');
    }

    // ─── Assessment Levels ────────────────────────────────────────────────────────

    public function storeAssessmentLevel(Request $request)
    {
        $request->validate([
            'rpta_actual_use_id' => 'required|exists:rpta_actual_uses,id',
            'revision_year_id'   => 'required|exists:rpta_revision_years,id',
            'min_value'          => 'required|numeric|min:0',
            'max_value'          => 'nullable|numeric|gt:min_value',
            'rate'               => 'required|numeric|min:0|max:100',
        ]);

        $rateDecimal = $request->rate / 100;

        // Check for duplicate
        $exists = RptaAssessmentLevel::where('rpta_actual_use_id', $request->rpta_actual_use_id)
            ->where('revision_year_id', $request->revision_year_id)
            ->where('min_value', $request->min_value)
            ->where('max_value', $request->max_value)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors('An assessment level already exists for this Actual Use and Market Value range in the selected Revision Year.');
        }

        RptaAssessmentLevel::create([
            'rpta_actual_use_id' => $request->rpta_actual_use_id,
            'revision_year_id'   => $request->revision_year_id,
            'min_value'          => $request->min_value,
            'max_value'          => $request->max_value,
            'rate'               => $rateDecimal,
        ]);
        return back()->with('success', 'Assessment level added.');
    }

    public function destroyAssessmentLevel(RptaAssessmentLevel $level)
    {
        // Check if this specific rate is being used (optional, but good for integrity)
        $isUsed = \App\Models\RPT\FaasLand::where('rpta_actual_use_id', $level->rpta_actual_use_id)->where('assessment_level', $level->rate)->exists() ||
                  \App\Models\RPT\FaasBuilding::where('rpta_actual_use_id', $level->rpta_actual_use_id)->where('assessment_level', $level->rate)->exists() ||
                  \App\Models\RPT\FaasMachinery::where('rpta_actual_use_id', $level->rpta_actual_use_id)->where('assessment_level', $level->rate)->exists();

        if ($isUsed) {
            return back()->withErrors('Cannot remove this assessment level: It is already referenced by existing appraised components.');
        }

        $level->delete();
        return back()->with('success', 'Assessment level removed.');
    }

    // ─── Unit Values ─────────────────────────────────────────────────────────────

    public function storeUnitValue(Request $request)
    {
        $request->validate([
            'rpta_actual_use_id' => 'required|exists:rpta_actual_uses,id',
            'barangay_id'        => 'nullable|exists:barangays,id',
            'revision_year_id'   => 'required|exists:rpta_revision_years,id',
            'value_per_sqm'      => 'required|numeric|min:0',
        ]);

        $revYear = RptaRevisionYear::find($request->revision_year_id);

        // Check for duplicate
        $exists = RptaUnitValue::where('rpta_actual_use_id', $request->rpta_actual_use_id)
            ->where('barangay_id', $request->barangay_id)
            ->where('revision_year_id', $request->revision_year_id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors('A unit value already exists for this Actual Use and Location in the selected Revision Year.');
        }

        RptaUnitValue::create([
            'rpta_actual_use_id' => $request->rpta_actual_use_id,
            'barangay_id'        => $request->barangay_id,
            'revision_year_id'   => $request->revision_year_id,
            'value_per_sqm'      => $request->value_per_sqm,
            'effectivity_year'   => $revYear->year,
            'is_active'          => true,
        ]);

        return back()->with('success', 'Unit value added.');
    }

    public function destroyUnitValue(RptaUnitValue $unitValue)
    {
        // Check if this land value is being used
        $isUsed = \App\Models\RPT\FaasLand::where('rpta_actual_use_id', $unitValue->rpta_actual_use_id)
            ->where('unit_value', $unitValue->value_per_sqm)
            ->exists();

        if ($isUsed) {
            return back()->withErrors('Cannot remove this unit value: It is already referenced by existing land parcel appraisals.');
        }

        $unitValue->delete();
        return back()->with('success', 'Unit value removed.');
    }

    // ─── Building Types ───────────────────────────────────────────────────────────

    public function storeBldgType(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string',
            'code'                   => 'required|string|max:20|unique:rpta_bldg_types,code',
            'base_construction_cost' => 'required|numeric|min:0',
            'useful_life'            => 'required|numeric|min:1',
            'residual_value_rate'    => 'required|numeric|min:0|max:1',
        ]);
        RptaBldgType::create($request->only('name', 'code', 'base_construction_cost', 'useful_life', 'residual_value_rate'));
        return back()->with('success', 'Building type added.');
    }

    public function destroyBldgType(RptaBldgType $bldgType)
    {
        $bldgType->delete();
        return back()->with('success', 'Building type removed.');
    }

    // ─── Revision Year ────────────────────────────────────────────────────────────

    public function storeRevisionYear(Request $request)
    {
        $request->validate(['year' => 'required|integer|min:2000|unique:rpta_revision_years,year']);
        
        $isCurrent = $request->boolean('is_current');

        if ($isCurrent) {
            RptaRevisionYear::where('is_current', true)->update(['is_current' => false]);
        }

        RptaRevisionYear::create(['year' => $request->year, 'is_current' => $isCurrent]);
        return back()->with('success', 'Revision year added.');
    }

    public function destroyRevisionYear(RptaRevisionYear $year)
    {
        if (\App\Models\RPT\FaasProperty::where('revision_year_id', $year->id)->exists()) {
            return back()->withErrors('Cannot delete revision year: It is already referenced by existing FAAS records.');
        }

        if ($year->is_current) {
            return back()->withErrors('Cannot delete the current active revision year.');
        }

        $year->delete();
        return back()->with('success', 'Revision year removed.');
    }

    public function setCurrentRevisionYear(RptaRevisionYear $year)
    {
        RptaRevisionYear::where('is_current', true)->update(['is_current' => false]);
        $year->update(['is_current' => true]);
        return back()->with('success', 'Revision year set as current.');
    }

    // ─── Signatories ──────────────────────────────────────────────────────────────

    public function storeSignatory(Request $request)
    {
        $request->validate(['role' => 'required|string', 'name' => 'required|string', 'designation' => 'nullable|string']);
        \DB::table('rpta_signatories')->insert(['role' => $request->role, 'name' => $request->name, 'designation' => $request->designation, 'created_at' => now(), 'updated_at' => now()]);
        return back()->with('success', 'Signatory added.');
    }

    public function updateSignatory(Request $request, int $id)
    {
        \DB::table('rpta_signatories')->where('id', $id)->update(['name' => $request->name, 'designation' => $request->designation, 'updated_at' => now()]);
        return back()->with('success', 'Signatory updated.');
    }
}
