<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Barangay;
use App\Models\User;

class RptOnlineApplication extends Model
{
    use SoftDeletes;

    protected $table = 'rpt_online_applications';

    protected $fillable = [
        'reference_no', 'client_id',
        'owner_name', 'owner_tin', 'owner_address', 'owner_contact', 'owner_email',
        'barangay_id', 'street', 'municipality', 'province',
        'property_type', 'lot_no', 'blk_no', 'survey_no', 'title_no', 'land_area',
        'property_description', 'status', 'staff_remarks', 'reviewed_by', 'reviewed_at',
        'faas_property_id',
    ];

    protected $casts = [
        'land_area'   => 'decimal:4',
        'reviewed_at' => 'datetime',
    ];

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function faasProperty(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'faas_property_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RptApplicationDocument::class, 'rpt_online_application_id');
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }

    /**
     * Generate a unique reference number for the online application.
     */
    public static function generateReferenceNo(): string
    {
        do {
            $ref = 'RPT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        } while (self::where('reference_no', $ref)->exists());

        return $ref;
    }
}
