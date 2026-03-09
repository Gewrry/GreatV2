<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * Immutable append-only TD audit log.
 * No updated_at to prevent modification.
 */
class TdActivityLog extends Model
{
    const UPDATED_AT = null; // immutable — no updated_at column

    protected $table = 'td_activity_logs';

    protected $fillable = [
        'tax_declaration_id',
        'user_id',
        'action',
        'description',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function taxDeclaration()
    {
        return $this->belongsTo(TaxDeclaration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convenience factory — stamps the currently authenticated user automatically.
     */
    public static function record(int $tdId, string $action, string $description, array $meta = []): self
    {
        return self::create([
            'tax_declaration_id' => $tdId,
            'user_id'            => Auth::id(),
            'action'             => $action,
            'description'        => $description,
            'meta'               => $meta ?: null,
        ]);
    }
}
