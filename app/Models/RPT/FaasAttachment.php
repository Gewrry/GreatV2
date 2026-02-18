<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasAttachment extends Model
{
    use HasFactory;

    protected $table = 'faas_attachments';

    protected $fillable = [
        'faas_id',
        'file_path',
        'file_name',
        'file_type',
        'description',
        'attachment_type',
    ];

    public function faas()
    {
        return $this->belongsTo(FaasGenRev::class, 'faas_id');
    }
}
