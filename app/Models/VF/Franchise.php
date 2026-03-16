<?php
// app/Models/VF/Franchise.php

namespace App\Models\VF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Franchise extends Model
{
    use SoftDeletes;

    protected $table = 'vf_franchises';

    protected $fillable = [
        'fn_number',
        'permit_number',
        'permit_date',
        'permit_type',
        'owner_id',
        'toda_id',
        'driver_name',
        'driver_contact',
        'license_number',
        'remarks',
        'status',
        'encoded_by',
    ];

    protected $casts = [
        'permit_date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function owner()
    {
        return $this->belongsTo(FranchiseOwner::class, 'owner_id');
    }

    public function toda()
    {
        return $this->belongsTo(Toda::class, 'toda_id');
    }

    public function vehicle()
    {
        return $this->hasOne(FranchiseVehicle::class, 'franchise_id');
    }

    public function history()
    {
        return $this->hasMany(FranchiseHistory::class, 'franchise_id');
    }

    public function encodedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'encoded_by');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Get the next FN number (max + 1).
     */
    public static function nextFnNumber(): int
    {
        return (static::withTrashed()->max('fn_number') ?? 0) + 1;
    }

    /**
     * Get the next permit number in format  {seq}-{year}.
     */
    public static function nextPermitNumber(): string
    {
        $year = now()->year;
        $last = static::withTrashed()
            ->whereYear('created_at', $year)
            ->orderByDesc('id')
            ->value('permit_number');

        if ($last) {
            $seq = (int) explode('-', $last)[0];
            return ($seq + 1) . '-' . $year;
        }

        return '1-' . $year;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeFilter($query, $request)
    {
        return $query
            ->when($request->search, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('fn_number', 'like', "%{$s}%")
                        ->orWhere('permit_number', 'like', "%{$s}%")
                        ->orWhereHas(
                            'owner',
                            fn($q) =>
                            $q->where(\Illuminate\Support\Facades\DB::raw("CONCAT(last_name, ' ', first_name)"), 'like', "%{$s}%")
                        )
                        ->orWhereHas(
                            'vehicle',
                            fn($q) =>
                            $q->where('plate_number', 'like', "%{$s}%")
                                ->orWhere('sticker_number', 'like', "%{$s}%")
                                ->orWhere('chassis_number', 'like', "%{$s}%")
                                ->orWhere('motor_number', 'like', "%{$s}%")
                        );
                });
            })
            ->when(
                $request->toda,
                fn($q, $v) =>
                $q->whereHas('toda', fn($q) => $q->where('name', $v))
            )
            ->when(
                $request->barangay,
                fn($q, $v) =>
                $q->whereHas('owner', fn($q) => $q->where('barangay', $v))
            )
            ->when(
                $request->type,
                fn($q, $v) =>
                $q->where('permit_type', $v)
            )
            ->when(
                $request->year,
                fn($q, $v) =>
                $q->whereYear('permit_date', $v)
            );
    }



    // Relationships
    public function payments()
    {
        return $this->hasMany(\App\Models\VF\Payment::class);
    }

    // Accessors
    public function getOwnerNameAttribute(): string
    {
        if (!$this->owner)
            return '—';
        return trim("{$this->owner->last_name}, {$this->owner->first_name} " . ($this->owner->middle_name ? substr($this->owner->middle_name, 0, 1) . '.' : ''));
    }

    public function getPlateNumberAttribute(): ?string
    {
        return $this->vehicle?->plate_number;
    }

    public function getBangayAttribute(): ?string
    {
        return $this->owner?->barangay;
    }
}