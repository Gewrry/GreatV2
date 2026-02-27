<?php
// app/Models/onlineBPLS/BplsDocument.php

namespace App\Models\onlineBPLS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class BplsDocument extends Model
{
    use SoftDeletes;

    protected $table = 'bpls_documents';

    protected $fillable = [
        'bpls_application_id',
        'document_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'status',
        'rejection_reason',
    ];

    const TYPES = [
        'dti_sec_cda' => 'DTI/SEC/CDA Certificate',
        'barangay_clearance' => 'Barangay Clearance',
        'community_tax' => 'Community Tax Certificate',
        'lease_contract' => 'Lease Contract / Owner\'s Consent',
        'fire_clearance' => 'Fire Safety Inspection Certificate',
        'sanitary_permit' => 'Sanitary Permit',
        'beneficiary_senior' => 'Senior Citizen Proof (ID/OSCA)',
        'beneficiary_pwd' => 'PWD Proof (ID/PDAO)',
        'beneficiary_bmbe' => 'BMBE Certification',
        'beneficiary_cooperative' => 'CDA Registration / Certificate of Good Standing',
        'beneficiary_solo_parent' => 'Solo Parent ID',
        'others' => 'Other Documents',
    ];

    const REQUIRED_TYPES = [
        'dti_sec_cda',
        'barangay_clearance',
        'community_tax',
    ];

    public function application()
    {
        return $this->belongsTo(BplsApplication::class, 'bpls_application_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->document_type] ?? ucfirst(str_replace('_', ' ', $this->document_type));
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576)
            return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)
            return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'verified' => 'bg-green-100 text-green-700 border-green-200',
            'rejected' => 'bg-red-100 text-red-700 border-red-200',
            default => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        };
    }
}