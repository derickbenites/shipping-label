<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateShippingLabelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Origin Address (From) - US Only
            'from_name' => 'required|string|max:255',
            'from_company' => 'nullable|string|max:255',
            'from_street1' => 'required|string|max:255',
            'from_street2' => 'nullable|string|max:255',
            'from_city' => 'required|string|max:255',
            'from_state' => 'required|string|size:2|uppercase',
            'from_zip' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
            'from_phone' => 'nullable|string|max:20',

            // Destination Address (To) - US Only
            'to_name' => 'required|string|max:255',
            'to_company' => 'nullable|string|max:255',
            'to_street1' => 'required|string|max:255',
            'to_street2' => 'nullable|string|max:255',
            'to_city' => 'required|string|max:255',
            'to_state' => 'required|string|size:2|uppercase',
            'to_zip' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
            'to_phone' => 'nullable|string|max:20',

            // Package Details
            'weight' => 'required|numeric|min:0.1|max:1120', // Max 70 lbs in oz
            'length' => 'nullable|numeric|min:0.1|max:108',
            'width' => 'nullable|numeric|min:0.1|max:108',
            'height' => 'nullable|numeric|min:0.1|max:108',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'from_state.size' => 'The origin state must be a 2-letter US state code (e.g., CA, NY).',
            'to_state.size' => 'The destination state must be a 2-letter US state code (e.g., CA, NY).',
            'from_zip.regex' => 'The origin ZIP code must be in format 12345 or 12345-6789.',
            'to_zip.regex' => 'The destination ZIP code must be in format 12345 or 12345-6789.',
            'weight.max' => 'The maximum weight is 1120 ounces (70 pounds).',
        ];
    }
}
