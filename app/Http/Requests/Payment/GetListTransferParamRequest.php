<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class GetListTransferParamRequest extends FormRequest
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
    /**
     * Normalize inputs before validation.
     *
     * Some clients or URL encoders transform the '+' sign in timezone offsets
     * into a space when application/x-www-form-urlencoded is used. Example:
     *  - Original: 2025-07-21T08:35:34+07:00
     *  - Decoded:  2025-07-21T08:35:34 07:00
     *
     * This method restores the '+' when appropriate so the `date_format`
     * validator can match the ISO 8601 format.
     */
    protected function prepareForValidation(): void
    {
        foreach (['fromDate', 'toDate'] as $field) {
            if ($this->filled($field)) {
                $value = $this->input($field);

                // Replace a space between the seconds and the timezone with '+' when
                // the timezone appears as either '+07:00', '-07:00' or '07:00' but
                // the plus was converted to a space.
                $normalized = preg_replace('/(\d{2}:\d{2}:\d{2})\s+([+-]?\d{2}:\d{2})$/', '$1+$2', $value);

                if ($normalized !== $value) {
                    $this->merge([$field => $normalized]);
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'referenceId' => 'nullable|string',
            'approvalState' => 'nullable|string',
            'category' => 'nullable|array',
            'category.*' => 'string',
            // Accept ISO 8601 format with timezone offset, e.g. 2025-07-21T08:35:34+07:00
            'fromDate' => 'nullable|date_format:Y-m-d\\TH:i:sP',
            'toDate' => 'nullable|date_format:Y-m-d\\TH:i:sP|after_or_equal:fromDate',
            'limit' => 'nullable|integer|min:0|max:100',
            'offset' => 'nullable|integer|min:0'
        ];
    }

    public function message(): array
    {
        return [
            'fromDate.date_format' => 'The fromDate must be in ISO 8601 format, e.g. 2025-07-21T08:35:34+07:00',
            'toDate.date_format' => 'The toDate must be in ISO 8601 format, e.g. 2025-07-21T08:35:34+07:00',
            'toDate.after_or_equal' => 'The toDate must be after or equal to the fromDate',
        ];
    }
}
