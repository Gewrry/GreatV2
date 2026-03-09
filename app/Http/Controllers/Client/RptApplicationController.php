<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\RPT\RptOnlineApplication;
use App\Models\RPT\RptApplicationDocument;
use App\Models\RPT\ClientLinkedProperty;
use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\RptBilling;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RptApplicationController extends Controller
{
    /**
     * Display the client's RPT applications and linked properties.
     */
    public function index()
    {
        $clientId = Auth::guard('client')->id();

        $applications = RptOnlineApplication::where('client_id', $clientId)
            ->latest()
            ->paginate(10);

        // Load linked properties with their TD and billing status
        $linkedProperties = ClientLinkedProperty::where('client_id', $clientId)
            ->with(['taxDeclaration.property.barangay'])
            ->latest('linked_at')
            ->get();

        // Calculate current balance for each linked property
        foreach ($linkedProperties as $link) {
            $td = $link->taxDeclaration;
            if ($td) {
                $unpaid = RptBilling::where('tax_declaration_id', $td->id)
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->sum('balance');
                $link->current_balance = $unpaid;
            } else {
                $link->current_balance = 0;
            }
        }

        return view('client.rpt.index', compact('applications', 'linkedProperties'));
    }

    /**
     * Show the online registration form.
     */
    public function create()
    {
        $barangays = Barangay::orderBy('brgy_name')->get();
        return view('client.rpt.create', compact('barangays'));
    }

    /**
     * Store a new online application with documents.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_name'          => 'required|string|max:255',
            'owner_tin'           => 'nullable|string|max:20',
            'owner_address'       => 'required|string|max:500',
            'owner_contact'       => 'nullable|string|max:50',
            'owner_email'         => 'nullable|email|max:255',
            'barangay_id'         => 'nullable|exists:barangays,id',
            'street'              => 'nullable|string|max:255',
            'municipality'        => 'nullable|string|max:255',
            'province'            => 'nullable|string|max:255',
            'property_type'       => 'required|in:land,building,machinery,mixed',
            'title_no'            => 'nullable|string|max:100',
            'property_description'=> 'nullable|string|max:2000',

            // Documents
            'documents'           => 'nullable|array',
            'documents.*.file'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.*.type'    => 'required|string',
            'documents.*.label'   => 'nullable|string|max:255',
        ]);

        $application = RptOnlineApplication::create([
            'reference_no'        => RptOnlineApplication::generateReferenceNo(),
            'client_id'           => Auth::guard('client')->id(),
            'owner_name'          => $validated['owner_name'],
            'owner_tin'           => $validated['owner_tin'] ?? null,
            'owner_address'       => $validated['owner_address'],
            'owner_contact'       => $validated['owner_contact'] ?? null,
            'owner_email'         => $validated['owner_email'] ?? null,
            'barangay_id'         => $validated['barangay_id'] ?? null,
            'street'              => $validated['street'] ?? null,
            'municipality'        => $validated['municipality'] ?? null,
            'province'            => $validated['province'] ?? null,
            'property_type'       => $validated['property_type'],
            'title_no'            => $validated['title_no'] ?? null,
            'property_description'=> $validated['property_description'] ?? null,
            'status'              => 'pending',
        ]);

        // Upload documents
        if ($request->has('documents')) {
            foreach ($request->file('documents', []) as $index => $docData) {
                if (!isset($docData['file'])) continue;
                $path = $docData['file']->store('rpt/online-applications/' . $application->id, 'public');
                RptApplicationDocument::create([
                    'rpt_online_application_id' => $application->id,
                    'type'                      => $request->input("documents.{$index}.type"),
                    'label'                     => $request->input("documents.{$index}.label"),
                    'file_path'                 => $path,
                    'original_filename'         => $docData['file']->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('client.rpt.show', $application)
            ->with('success', 'Your application has been submitted! Reference No.: ' . $application->reference_no);
    }

    /**
     * Show a single application (status tracking).
     */
    public function show(RptOnlineApplication $application)
    {
        // Ensure the client can only see their own applications
        abort_if($application->client_id !== Auth::guard('client')->id(), 403);
        $application->load('barangay', 'documents');
        return view('client.rpt.show', compact('application'));
    }

    /**
     * Link a property (Tax Declaration) to the client's account.
     */
    public function linkProperty(Request $request)
    {
        $request->validate([
            'td_no' => 'required|string|max:100',
            'nickname' => 'nullable|string|max:100',
        ]);

        $td = TaxDeclaration::where('td_no', $request->td_no)
            ->where('status', 'forwarded')
            ->first();

        if (!$td) {
            return back()->with('error', 'No forwarded Tax Declaration found with TD No. "' . $request->td_no . '". Make sure the property has been forwarded by the Assessor\'s Office.');
        }

        $clientId = Auth::guard('client')->id();

        // Check for duplicate
        $exists = ClientLinkedProperty::where('client_id', $clientId)
            ->where('tax_declaration_id', $td->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This property is already linked to your account.');
        }

        ClientLinkedProperty::create([
            'client_id'          => $clientId,
            'tax_declaration_id' => $td->id,
            'nickname'           => $request->nickname,
            'linked_at'          => now(),
        ]);

        return back()->with('success', '✅ Property with TD No. "' . $td->td_no . '" has been linked to your account!');
    }

    /**
     * Unlink a property from the client's account.
     */
    public function unlinkProperty(ClientLinkedProperty $link)
    {
        abort_if($link->client_id !== Auth::guard('client')->id(), 403);
        $link->delete();
        return back()->with('success', 'Property has been unlinked from your account.');
    }
}
