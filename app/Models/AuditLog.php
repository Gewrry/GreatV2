<?php
// app/Models/AuditLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    // -------------------------------------------------------------------------
    // Mass-assignable columns
    // -------------------------------------------------------------------------
    protected $fillable = [
        'user_id',
        'user_name',
        'module',
        'action',           // created | updated | deleted | viewed | payment | status_change | login | logout | export
        'description',
        'model_type',       // e.g. App\Models\BusinessEntry
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',           // GET | POST | PUT | PATCH | DELETE
        'status',           // success | failed | warning
        'extra',            // arbitrary JSON payload
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'extra' => 'array',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /** Filter by module name */
    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /** Filter by action */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /** Filter by specific model */
    public function scopeForModel($query, string $type, int $id)
    {
        return $query->where('model_type', $type)->where('model_id', $id);
    }

    /** Filter by status */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // -------------------------------------------------------------------------
    // Static helper — record a log entry from anywhere
    // -------------------------------------------------------------------------

    /**
     * Quick log creation.
     *
     * Usage:
     *   AuditLog::record('BPLS', 'payment', 'Payment recorded for ' . $entry->business_name, $entry);
     *
     * @param  string       $module
     * @param  string       $action    created|updated|deleted|payment|status_change|login|logout|export|viewed
     * @param  string       $description
     * @param  mixed|null   $model     Eloquent model instance (optional)
     * @param  array        $options   ['old_values', 'new_values', 'extra', 'status']
     */
    public static function record(
        string $module,
        string $action,
        string $description,
        mixed $model = null,
        array $options = []
    ): self {
        $request = request();
        $user = auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user ? ($user->uname ?? ($user->employee->first_name ?? '') . ' ' . ($user->employee->last_name ?? '')) : 'System',
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $options['old_values'] ?? null,
            'new_values' => $options['new_values'] ?? null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'status' => $options['status'] ?? 'success',
            'extra' => $options['extra'] ?? null,
        ]);
    }
}