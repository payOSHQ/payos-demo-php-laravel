<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreateBatchTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => 'nullable|array',
            'category.*' => 'string',
            'validateDestination' => 'nullable|boolean:strict',
            'payouts' => 'required|array',
            'payouts.*.amount' => 'required|integer',
            'payouts.*.description' => 'required|string',
            'payouts.*.toBin' => 'required|string',
            'payouts.*.toAccountNumber' => 'required|string'
            //
        ];
    }
}
