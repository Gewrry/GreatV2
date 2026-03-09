<?php

namespace App\Http\Requests\RPT;

use Illuminate\Foundation\Http\FormRequest;

class StoreTdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'faas_property_id'   => 'required|exists:faas_properties,id',
            'component_type'     => 'required|in:land,building,machinery',
            'component_id'       => 'required|integer|min:1',
            'effectivity_year'   => 'required|integer|min:2000',
            'declaration_reason' => 'required|string|max:500',
            'tax_rate'           => 'required|numeric|min:0|max:1',
            'is_taxable'         => 'boolean',
        ];
    }
}
