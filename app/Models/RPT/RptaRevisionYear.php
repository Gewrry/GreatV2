<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RptaRevisionYear extends Model
{
    protected $table = 'rpta_revision_years';
    protected $fillable = ['year', 'is_current'];

    public function taxDeclarations(): HasMany
    {
        return $this->hasMany(TaxDeclaration::class, 'revision_year_id');
    }

    public static function current(): ?self
    {
        return self::where('is_current', true)->first();
    }
}
