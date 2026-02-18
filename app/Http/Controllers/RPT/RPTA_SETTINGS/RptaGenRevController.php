<?php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;

use App\Models\RPT\FaasGenRev;
use App\Models\RPT\RptAuValue;
use App\Models\Barangay;
use App\Models\FaasLandTbl1;
use App\Models\FaasBuilding1;
use App\Models\FaasMachineTbl1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RptaGenRevController extends Controller
{
    public function index()
    {
        $revisions = FaasGenRev::with('barangay')
            ->orderBy('encoded_date', 'desc')
            ->get();
            
        $barangays = Barangay::orderBy('brgy_code')->get();
        $unitValueDates = RptAuValue::select('rev_date')
            ->distinct()
            ->orderBy('rev_date', 'desc')
            ->pluck('rev_date');
            
        return view('modules.rpt.rpta_settings.rpta_gen_rev', compact('revisions', 'barangays', 'unitValueDates'));
    }

    public function getMaxRevisionYear(Request $request)
    {
        $kind = $request->get('kind');
        
        if (empty($kind)) {
            return response()->json([]);
        }

        $years = [];
        
        switch ($kind) {
            case 'land':
                $years = FaasLandTbl1::select('faas_land_tbl9.year as revision_year')
                    ->join('faas_land_tbl9', 'faas_land_tbl9.uarp_no', '=', 'faas_land_tbl1.uarp_no')
                    ->whereNotNull('faas_land_tbl1.pin')
                    ->where('faas_land_tbl1.pin', '!=', '')
                    ->distinct()
                    ->orderBy('faas_land_tbl9.year', 'desc')
                    ->pluck('revision_year');
                break;
                
            case 'building':
                $years = FaasBuilding1::select('faas_building7.p_year as revision_year')
                    ->join('faas_building7', 'faas_building7.uarp_no', '=', 'faas_building1.uarp_no')
                    ->whereNotNull('faas_building1.pin')
                    ->where('faas_building1.pin', '!=', '')
                    ->distinct()
                    ->orderBy('faas_building7.p_year', 'desc')
                    ->pluck('revision_year');
                break;
                
            case 'machine':
                $years = FaasMachineTbl1::select('faas_machine_tbl44.yr as revision_year')
                    ->join('faas_machine_tbl44', 'faas_machine_tbl44.uarp_no', '=', 'faas_machine_tbl1.uarp_no')
                    ->whereNotNull('faas_machine_tbl1.pin_no')
                    ->where('faas_machine_tbl1.pin_no', '!=', '')
                    ->distinct()
                    ->orderBy('faas_machine_tbl44.yr', 'desc')
                    ->pluck('revision_year');
                break;
        }
        
        return response()->json($years);
    }

    public function processRevision(Request $request)
    {
        $validated = $request->validate([
            'kind' => 'required|in:land,building,machine',
            'revised_year' => 'required|integer|min:2000|max:' . (date('Y') + 10),
            'gen_rev' => 'required|integer|min:2000|max:' . (date('Y') + 10),
            'bcode' => 'required|string|max:10',
            'rev_unit_val' => 'required|string|max:50',
            'entry_date' => 'nullable|date',
            'entry_by' => 'nullable|string|max:255',
        ]);

        $existing = FaasGenRev::where('kind', $validated['kind'])
            ->where('revised_year', $validated['revised_year'])
            ->where('bcode', $validated['bcode'])
            ->where('statt', 'revised')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Revision already exists for this combination.'
            ], 422);
        }

        $result = $this->processRevisionByKind($validated);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        $revision = FaasGenRev::create([
            'kind' => $validated['kind'],
            'td_no' => 'GR-' . strtoupper($validated['kind']) . '-' . $validated['gen_rev'] . '-' . $validated['bcode'],
            'revised_year' => $validated['revised_year'],
            'gen_rev' => $validated['gen_rev'],
            'bcode' => $validated['bcode'],
            'rev_unit_val' => $validated['rev_unit_val'],
            'gen_desc' => $result['description'] ?? '',
            'statt' => 'revised',
            'encoded_by' => Auth::user()->uname ?? Auth::user()->name ?? 'system',
            'entry_date' => $validated['entry_date'],
            'entry_by' => $validated['entry_by'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'RPU have been ADDED for General Revision.',
            'data' => $revision
        ]);
    }

    private function processRevisionByKind($data)
    {
        $kind = $data['kind'];
        $revisedYear = $data['revised_year'];
        $newYear = $data['gen_rev'];
        $bcode = $data['bcode'];
        $revUnitVal = $data['rev_unit_val'];

        try {
            DB::beginTransaction();

            switch ($kind) {
                case 'land':
                    $this->processLandRevision($revisedYear, $newYear, $bcode, $revUnitVal);
                    break;
                    
                case 'building':
                    $this->processBuildingRevision($revisedYear, $newYear, $bcode, $revUnitVal);
                    break;
                    
                case 'machine':
                    $this->processMachineRevision($revisedYear, $newYear, $bcode, $revUnitVal);
                    break;
            }

            DB::commit();

            return [
                'success' => true,
                'description' => "General revision for {$kind} properties in barangay {$bcode} from year {$revisedYear} to {$newYear}"
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error processing revision: ' . $e->getMessage()
            ];
        }
    }

    private function processLandRevision($revisedYear, $newYear, $bcode, $revUnitVal)
    {
        $lands = FaasLandTbl1::select('faas_land_tbl1.*', 'faas_land_tbl2.brgy', 'faas_land_tbl9.year')
            ->join('faas_land_tbl2', 'faas_land_tbl2.uarp_no', '=', 'faas_land_tbl1.uarp_no')
            ->join('faas_land_tbl9', 'faas_land_tbl9.uarp_no', '=', 'faas_land_tbl1.uarp_no')
            ->where('faas_land_tbl2.brgy', $bcode)
            ->where('faas_land_tbl9.year', $revisedYear)
            ->whereNotNull('faas_land_tbl1.pin')
            ->where('faas_land_tbl1.pin', '!=', '')
            ->get();

        foreach ($lands as $land) {
            DB::table('faas_land_tbl9')
                ->where('uarp_no', $land->uarp_no)
                ->update([
                    'year' => $newYear,
                    'gr' => $newYear
                ]);
        }
    }

    private function processBuildingRevision($revisedYear, $newYear, $bcode, $revUnitVal)
    {
        $buildings = FaasBuilding1::select('faas_building1.*', 'faas_building2.bldg_brgy', 'faas_building7.p_year')
            ->join('faas_building2', 'faas_building2.uarp_no', '=', 'faas_building1.uarp_no')
            ->join('faas_building7', 'faas_building7.uarp_no', '=', 'faas_building1.uarp_no')
            ->where('faas_building2.bldg_brgy', $bcode)
            ->where('faas_building7.p_year', $revisedYear)
            ->whereNotNull('faas_building1.pin')
            ->where('faas_building1.pin', '!=', '')
            ->get();

        foreach ($buildings as $building) {
            DB::table('faas_building7')
                ->where('uarp_no', $building->uarp_no)
                ->update([
                    'p_year' => $newYear,
                    'gr' => $newYear
                ]);
        }
    }

    private function processMachineRevision($revisedYear, $newYear, $bcode, $revUnitVal)
    {
        $machines = FaasMachineTbl1::select('faas_machine_tbl1.*', 'faas_machine_tbl44.yr')
            ->join('faas_machine_tbl44', 'faas_machine_tbl44.uarp_no', '=', 'faas_machine_tbl1.uarp_no')
            ->where('faas_machine_tbl1.p_brgy', $bcode)
            ->where('faas_machine_tbl44.yr', $revisedYear)
            ->whereNotNull('faas_machine_tbl1.pin_no')
            ->where('faas_machine_tbl1.pin_no', '!=', '')
            ->get();

        foreach ($machines as $machine) {
            DB::table('faas_machine_tbl44')
                ->where('uarp_no', $machine->uarp_no)
                ->update([
                    'yr' => $newYear,
                    'gr' => $newYear
                ]);
        }
    }

    public function getRevisions(Request $request)
    {
        $revisions = FaasGenRev::with('barangay')
            ->orderBy('encoded_date', 'desc')
            ->get()
            ->map(function ($revision) {
                return [
                    'id' => $revision->id,
                    'kind' => strtoupper($revision->kind),
                    'revised_year' => $revision->revised_year,
                    'gen_rev' => $revision->gen_rev,
                    'bcode' => $revision->bcode,
                    'bname' => $revision->barangay ? $revision->barangay->brgy_name : '',
                    'statt' => $revision->statt,
                    'encoded_by' => $revision->encoded_by,
                    'encoded_date' => $revision->encoded_date->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($revisions);
    }

    public function cancelRevision($id)
    {
        $revision = FaasGenRev::findOrFail($id);
        $this->reverseRevision($revision);
        $revision->delete();

        return response()->json([
            'success' => true,
            'message' => 'Revision has been cancelled successfully.'
        ]);
    }

    private function reverseRevision($revision)
    {
    }
}
