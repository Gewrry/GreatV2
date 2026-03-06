<?php

namespace App\Http\Requests\RPT;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'or_no'         => ['required', 'string', 'max:50', 'regex:/^[0-9\-]+$/', 'unique:rpt_payments,or_no'],
            'amount_paid'   => 'required|numeric|min:0.01',
            'payment_mode'  => 'required|in:cash,check,online',
            'payment_date'  => 'required|date|before_or_equal:today',
            'check_no'      => 'required_if:payment_mode,check|nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'or_no.regex' => 'The O.R. Number must only contain numbers and hyphens.',
            'or_no.unique' => 'This O.R. Number has already been used.',
        ];
    }
}
