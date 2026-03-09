<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class RptApplicationDocument extends Model
{
    use SoftDeletes;

    protected $table = 'rpt_application_documents';

    protected $fillable = [
        'rpt_online_application_id', 'type', 'label', 'file_path', 'original_filename',
        'verification_status', 'rejection_reason', 'verified_by', 'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(RptOnlineApplication::class, 'rpt_online_application_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending(): bool  { return $this->verification_status === 'pending'; }
    public function isVerified(): bool { return $this->verification_status === 'verified'; }
    public function isRejected(): bool { return $this->verification_status === 'rejected'; }
}
