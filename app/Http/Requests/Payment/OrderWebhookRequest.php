<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class OrderWebhookRequest extends FormRequest
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
            'code' => 'required|string',
            'desc' => 'required|string',
            'success' => 'nullable|boolean:strict',
            'signature' => 'required|string',
            'data' => 'required|array',

            'data.orderCode' => 'required|integer',
            'data.amount' => 'required|integer',
            'data.description' => 'required|string',
            'data.accountNumber' => 'required|string',
            'data.reference' => 'required|string',
            'data.transactionDateTime' => 'required|string',
            'data.currency' => 'required:string',
            'data.paymentLinkId' => 'required|string',
            'data.code' => 'required|string',
            'data.desc' => 'required|string',
            'data.counterAccountBankId' => 'nullable|string',
            'data.counterAccountBankName' => 'nullable|string',
            'data.counterAccountName' => 'nullable|string',
            'data.counterAccountNumber' => 'nullable|string',
            'data.virtualAccountName' => 'nullable|string',
            'data.virtualAccountNumber' => 'nullable|string'
        ];
    }
}
