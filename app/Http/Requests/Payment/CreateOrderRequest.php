<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'amount' => 'required|integer',
            'description' => 'required|string|max:25',
            'returnUrl' => 'required|url',
            'cancelUrl' => 'required|url',
            'expiredAt' => 'nullable|integer',

            'buyerName' => 'nullable|string',
            'buyerCompanyName' => 'nullable|string',
            'buyerTaxCode' => 'nullable|string',
            'buyerEmail' => 'nullable|string|email',
            'buyerPhone' => 'nullable|string',
            'buyerAddress' => 'nullable|string',

            'items' => 'nullable|array',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|integer',
            'items.*.quantity' => 'required|integer',
            'items.*.unit' => 'nullable|string',
            'items.*.taxPercentage' => 'nullable|integer',

            'invoice' => 'nullable|array',
            'invoice.buyerNotGetInvoice' => 'nullable|boolean:strict',
            'invoice.taxPercentage' => 'nullable|integer'
            //
        ];
    }
}
