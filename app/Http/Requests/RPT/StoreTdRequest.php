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
            'faas_property_id'    => 'required|exists:faas_properties,id',
            'component_type'      => 'required|in:land,building,machinery',
            'component_id'        => 'required|integer|min:1',
            'effectivity_year'    => 'required|integer|min:2000',
            'effectivity_quarter' => 'required|integer|between:1,4',
            'declaration_reason'  => 'required|string|max:500',
            'tax_rate'            => 'required|numeric|min:0|max:1',
            'is_taxable'          => 'boolean',
            'exemption_basis'     => 'nullable|string|max:255',
            'cancelled_td_no'     => 'nullable|string|max:100',
            'cancellation_reason' => 'nullable|string|max:1000',
        ];
    }
}
