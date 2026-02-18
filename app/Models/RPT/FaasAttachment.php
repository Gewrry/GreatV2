<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;

class FaasAttachment extends Model
{
    protected $fillable = [
        'faas_id',
        'file_path',
        'file_name',
        'file_type',
        'description'
    ];

    public function faas()
    {
        return $this->belongsTo(FaasGenRev::class, 'faas_id');
    }
}
