<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateMobileNumberRequest extends FormRequest
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
            'mobile_number' => [
                'required',
                'regex:/^\+91[6-9]\d{9}$/'
            ],
            'client_id' => [
                'integer',
            ],

        ];
    }

    public function messages()
    {
        return [
            'mobile_number.required' => 'The mobile number is required.',
            'mobile_number.regex' => 'The mobile number must be a valid Indian number starting with +91.',
            'client_id.integer' => 'The client ID must be an integer.',
        ];
    }
}
