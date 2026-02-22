<?php
// app/Http/Controllers/Client/DocumentUploadController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsApplication;
use App\Models\onlineBPLS\BplsDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentUploadController extends Controller
{
    // -----------------------------------------------------------------------
    // INDEX — show upload page
    // -----------------------------------------------------------------------
    public function index(BplsApplication $application)
    {
        $this->authorizeAccess($application);

        // Eager-load documents and key by document_type for O(1) lookups in the view
        $uploaded = $application->documents()
            ->get()
            ->keyBy('document_type'); // returns a Collection, always defined

        return view('client.applications.documents', [
            'application' => $application,
            'uploaded' => $uploaded,
        ]);
    }

    // -----------------------------------------------------------------------
    // UPLOAD — store a single document (replace if already exists)
    // -----------------------------------------------------------------------
    public function upload(Request $request, BplsApplication $application)
    {
        $this->authorizeAccess($application);

        $request->validate([
            'document_type' => 'required|in:' . implode(',', array_keys(BplsDocument::TYPES)),
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5 MB
        ]);

        $type = $request->document_type;

        // Delete old file if one already exists for this type
        $existing = $application->documents()->where('document_type', $type)->first();
        if ($existing) {
            Storage::disk('public')->delete($existing->file_path);
            $existing->delete();
        }

        $file = $request->file('file');
        $path = $file->store("bpls/applications/{$application->id}/documents", 'public');

        BplsDocument::create([
            'bpls_application_id' => $application->id,
            'document_type' => $type,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
        ]);

        return back()->with('success', BplsDocument::TYPES[$type] . ' uploaded successfully.');
    }

    // -----------------------------------------------------------------------
    // DESTROY — remove a document
    // -----------------------------------------------------------------------
    public function destroy(BplsApplication $application, BplsDocument $document)
    {
        $this->authorizeAccess($application);

        // Ensure document actually belongs to this application
        if ($document->bpls_application_id !== $application->id) {
            abort(403);
        }

        // Do not allow deleting already-verified documents
        if ($document->isVerified()) {
            return back()->with('error', 'Verified documents cannot be removed.');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document removed.');
    }

    // -----------------------------------------------------------------------
    // SUBMIT — transition draft → submitted
    // -----------------------------------------------------------------------
    public function submit(BplsApplication $application)
    {
        $this->authorizeAccess($application);

        // Check all required types are present
        $uploadedTypes = $application->documents()->pluck('document_type')->toArray();
        $missing = array_diff(BplsDocument::REQUIRED_TYPES, $uploadedTypes);

        if (!empty($missing)) {
            $labels = array_map(fn($t) => BplsDocument::TYPES[$t], $missing);
            return back()->with('error', 'Please upload the following required documents: ' . implode(', ', $labels));
        }

        $application->update([
            'workflow_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Optional: log the activity if BplsActivityLog exists
        if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class)) {
            \App\Models\onlineBPLS\BplsActivityLog::create([
                'bpls_application_id' => $application->id,
                'actor_type' => 'client',
                'actor_id' => Auth::guard('client')->id(),
                'action' => 'submitted',
                'from_status' => 'draft',
                'to_status' => 'submitted',
                'remarks' => 'Application submitted by client.',
            ]);
        }

        return redirect()
            ->route('client.applications.show', $application->id)
            ->with('success', 'Application ' . $application->application_number . ' submitted! Our team will review your documents shortly.');
    }

    // -----------------------------------------------------------------------
    // Authorization helper
    // -----------------------------------------------------------------------
    private function authorizeAccess(BplsApplication $application): void
    {
        $clientId = Auth::guard('client')->id();

        if ($application->client_id !== $clientId) {
            abort(403, 'Unauthorized.');
        }

        // For index/upload/destroy/submit — block if past the editable stages
        if (
            !in_array(request()->method(), ['GET']) &&
            !in_array($application->workflow_status, ['draft', 'returned'])
        ) {
            abort(403, 'Documents cannot be modified at this stage.');
        }
    }
}