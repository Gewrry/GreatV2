<?php
// app/Models/FaasGenRev.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasGenRev extends Model
{
    use HasFactory;

    protected $table = 'faas_gen_rev';
    protected $fillable = [
        'kind',
        'revised_year',
        'gen_rev',
        'bcode',
        'rev_unit_val',
        'gen_desc',
        'statt',
        'encoded_by',
        'entry_date',
        'entry_by'
    ];

    protected $casts = [
        'revised_year' => 'integer',
        'gen_rev' => 'integer',
        'entry_date' => 'date'
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'bcode', 'brgy_code');
    }
}