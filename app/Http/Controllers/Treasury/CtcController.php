<?php

namespace App\Http\Controllers\Treasury;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\CommunityTaxCertificate;
use Illuminate\Http\Request;

class CtcController extends Controller
{
    /**
     * Display a listing of CTC records.
     */
    public function list(Request $request)
    {
        $query = CommunityTaxCertificate::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('surname', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('ctc_number', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Year filter
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Sort by date issued (newest first)
        $query->orderBy('date_issued', 'desc');

        $ctcs   = $query->paginate(15)->withQueryString();
        $years  = CommunityTaxCertificate::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        // Summary stats
        $currentYear   = date('Y');
        $totalRecords  = CommunityTaxCertificate::count();
        $totalAmount   = CommunityTaxCertificate::sum('total_amount');
        $thisYearCount = CommunityTaxCertificate::where('year', $currentYear)->count();

        return view('modules.treasury.ctc.list', compact(
            'ctcs', 'years', 'totalRecords', 'totalAmount', 'thisYearCount', 'currentYear'
        ));
    }

    /**
     * Generate next CTC number and return as JSON (AJAX) or plain string.
     */
    public function generateNumber()
    {
        return response()->json(['ctc_number' => $this->generateCtcNumber()]);
    }

    /**
     * Generate next CTC number string.
     */
    public function generateCtcNumber(): string
    {
        $year   = date('Y');
        $prefix = "CTC-{$year}-";

        $lastCtc = CommunityTaxCertificate::where('ctc_number', 'like', "{$prefix}%")
            ->orderBy('ctc_number', 'desc')
            ->first();

        if ($lastCtc) {
            $lastNumber = (int) substr($lastCtc->ctc_number, -6);
            $newNumber  = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Display the CTC data entry form.
     */
    public function index(Request $request)
    {
        $barangays           = Barangay::orderBy('brgy_name')->get();
        $year                = date('Y');
        $defaultCitizenship  = 'FILIPINO';
        $defaultPlaceOfIssue = 'MTO-MAJAYJAY, LAGUNA';
        $defaultPlaceOfBirth = 'Majayjay, Laguna';
        $nextCtcNumber       = $this->generateCtcNumber();

        return view('modules.treasury.ctc.index', compact(
            'barangays', 'year', 'defaultCitizenship',
            'defaultPlaceOfIssue', 'defaultPlaceOfBirth', 'nextCtcNumber'
        ));
    }

    /**
     * Show a specific CTC record.
     */
    public function show($id)
    {
        $ctc = CommunityTaxCertificate::findOrFail($id);
        return view('modules.treasury.ctc.show', compact('ctc'));
    }

    /**
     * Display the CTC edit form.
     */
    public function edit($id)
    {
        $ctc                 = CommunityTaxCertificate::findOrFail($id);
        $barangays           = Barangay::orderBy('brgy_name')->get();
        $defaultCitizenship  = 'FILIPINO';
        $defaultPlaceOfIssue = 'MTO-MAJAYJAY, LAGUNA';

        return view('modules.treasury.ctc.edit', compact(
            'ctc', 'barangays', 'defaultCitizenship', 'defaultPlaceOfIssue'
        ));
    }

    /**
     * Process the CTC form submission and save to database.
     */
    public function store(Request $request)
    {
        $validated = $this->validateCtc($request);
        $taxData   = $this->calculateTaxData($validated);

        $barangay     = Barangay::find($validated['barangay_id']);
        $barangayName = $barangay ? $barangay->brgy_name : '';

        $ctc = CommunityTaxCertificate::create(array_merge(
            $this->buildCtcAttributes($validated, $barangayName),
            $taxData
        ));

        return redirect()->route('treasury.ctc.print', $ctc->id)->with('success', 'CTC created successfully!');
    }

    /**
     * Update an existing CTC record.
     */
    public function update(Request $request, $id)
    {
        $ctc       = CommunityTaxCertificate::findOrFail($id);
        $validated = $this->validateCtc($request, $id);
        $taxData   = $this->calculateTaxData($validated);

        $barangay     = Barangay::find($validated['barangay_id']);
        $barangayName = $barangay ? $barangay->brgy_name : '';

        $ctc->update(array_merge(
            $this->buildCtcAttributes($validated, $barangayName),
            $taxData
        ));

        return redirect()->route('treasury.ctc.list')->with('success', 'CTC updated successfully!');
    }

    /**
     * Delete a CTC record.
     */
    public function destroy($id)
    {
        $ctc = CommunityTaxCertificate::findOrFail($id);
        $ctc->delete();

        return redirect()->route('treasury.ctc.list')->with('success', 'CTC deleted successfully!');
    }

    /**
     * Print CTC receipt.
     */
    public function print($id)
    {
        $ctc = CommunityTaxCertificate::findOrFail($id);
        return view('modules.treasury.ctc.print', compact('ctc'));
    }

    // ─────────────────────── Private Helpers ────────────────────────────

    /**
     * Validate CTC form fields. Pass $id for update uniqueness rule.
     */
    private function validateCtc(Request $request, $id = null): array
    {
        $uniqueRule = $id
            ? "required|string|max:50|unique:community_tax_certificates,ctc_number,{$id}"
            : 'required|string|max:50|unique:community_tax_certificates,ctc_number';

        return $request->validate([
            'ctc_number'              => $uniqueRule,
            'year'                    => 'required|integer|min:2000|max:2100',
            'place_of_issue'          => 'required|string|max:255',
            'date_issued'             => 'required|date',
            'surname'                 => 'required|string|max:100',
            'first_name'              => 'required|string|max:100',
            'middle_name'             => 'nullable|string|max:100',
            'tin'                     => 'nullable|string|max:20',
            'address'                 => 'required|string|max:255',
            'barangay_id'             => 'required|integer',
            'gender'                  => 'required|in:MALE,FEMALE',
            'citizenship'             => 'nullable|string|max:50',
            'icr_number'              => 'nullable|string|max:50',
            'place_of_birth'          => 'nullable|string|max:255',
            'height'                  => 'nullable|numeric|min:0',
            'civil_status'            => 'required|in:SINGLE,MARRIED,WIDOWED,LEGALLY_SEPARATED',
            'date_of_birth'           => 'required|date',
            'weight'                  => 'nullable|numeric|min:0',
            'profession'              => 'nullable|string|max:150',
            'gross_receipts_business' => 'nullable|numeric|min:0',
            'salary_income'           => 'nullable|numeric|min:0',
            'salary_months'           => 'nullable|integer|min:1|max:12',
            'real_property_income'    => 'nullable|numeric|min:0',
            'interest_percent'        => 'nullable|numeric|min:0|max:100',
        ]);
    }

    /**
     * Compute all tax-related values from validated data and return as array.
     */
    private function calculateTaxData(array $validated): array
    {
        $basicTax = 5.00;

        $grossBusinessTax = 0;
        if (!empty($validated['gross_receipts_business'])) {
            $grossBusinessTax = floor($validated['gross_receipts_business'] / 1000);
        }

        $salaryTax = 0;
        if (!empty($validated['salary_income']) && !empty($validated['salary_months'])) {
            $annualSalary = $validated['salary_income'] * $validated['salary_months'];
            $salaryTax    = floor($annualSalary / 1000);
        }

        $realPropertyTax = 0;
        if (!empty($validated['real_property_income'])) {
            $realPropertyTax = floor($validated['real_property_income'] / 1000);
        }

        $additionalTax = min($grossBusinessTax + $salaryTax + $realPropertyTax, 5000);
        $subtotal      = $basicTax + $additionalTax;

        $interestPercent = $validated['interest_percent'] ?? 0;
        $interestAmount  = $subtotal * ($interestPercent / 100);
        $totalAmount     = $subtotal + $interestAmount;

        return [
            'basic_tax'                  => $basicTax,
            'gross_receipts_business'    => $validated['gross_receipts_business'] ?? 0,
            'gross_receipts_business_tax' => $grossBusinessTax,
            'salary_income'              => $validated['salary_income'] ?? 0,
            'salary_months'              => $validated['salary_months'] ?? 0,
            'salary_tax'                 => $salaryTax,
            'real_property_income'       => $validated['real_property_income'] ?? 0,
            'real_property_tax'          => $realPropertyTax,
            'additional_tax'             => $additionalTax,
            'interest_percent'           => $interestPercent,
            'interest_amount'            => $interestAmount,
            'total_amount'               => $totalAmount,
        ];
    }

    /**
     * Build the non-tax CTC attributes from validated data.
     */
    private function buildCtcAttributes(array $validated, string $barangayName): array
    {
        return [
            'ctc_number'     => $validated['ctc_number'],
            'year'           => $validated['year'],
            'place_of_issue' => $validated['place_of_issue'],
            'date_issued'    => $validated['date_issued'],
            'surname'        => $validated['surname'],
            'first_name'     => $validated['first_name'],
            'middle_name'    => $validated['middle_name'] ?? null,
            'tin'            => $validated['tin'] ?? null,
            'address'        => $validated['address'],
            'barangay_id'    => $validated['barangay_id'],
            'barangay_name'  => $barangayName,
            'gender'         => $validated['gender'],
            'citizenship'    => $validated['citizenship'] ?? 'FILIPINO',
            'icr_number'     => $validated['icr_number'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'height'         => $validated['height'] ?? null,
            'civil_status'   => $validated['civil_status'],
            'date_of_birth'  => $validated['date_of_birth'],
            'weight'         => $validated['weight'] ?? null,
            'profession'     => $validated['profession'] ?? null,
        ];
    }
}
