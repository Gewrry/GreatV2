<?php

namespace App\Http\Requests\RPT;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Owner / Declarant
            'owner_name'            => 'required|string|max:255',
            'owner_address'         => 'required|string|max:500',
            'owner_tin'             => 'nullable|string|max:50',
            'owner_contact'         => 'nullable|string|max:50',
            'owner_email'           => 'nullable|email|max:255',
            'administrator_name'    => 'nullable|string|max:255',
            'administrator_address' => 'nullable|string|max:500',

            // Location & Property Identification
            'barangay_id'    => 'required|exists:barangays,id',
            'property_type'  => 'required|in:land,building,machinery,mixed',
            'street'         => 'nullable|string|max:100',
            'municipality'   => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'title_no'       => 'nullable|string|max:100',
            'lot_no'         => 'nullable|string|max:100',
            'blk_no'         => 'nullable|string|max:100',
            'survey_no'      => 'nullable|string|max:100',
            'remarks'        => 'nullable|string|max:1000',

            // Supporting Documents (Dossier)
            'documents'         => 'nullable|array',
            'documents.*.file'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.*.type'  => 'required|string',
            'documents.*.label' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'barangay_id.required' => 'The property location (Barangay) is required for registry.',
        ];
    }
}
