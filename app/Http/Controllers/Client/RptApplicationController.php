<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\RPT\RptOnlineApplication;
use App\Models\RPT\RptApplicationDocument;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RptApplicationController extends Controller
{
    /**
     * Display the client's RPT applications.
     */
    public function index()
    {
        $applications = RptOnlineApplication::where('client_id', Auth::guard('client')->id())
            ->latest()
            ->paginate(10);

        return view('client.rpt.index', compact('applications'));
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
}
