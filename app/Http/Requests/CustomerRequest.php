<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $rules = [
            'first_name'    => 'sometimes|required|string|max:255',
            'last_name'     => 'sometimes|required|string|max:255',
            'phone'         => 'sometimes|required|string|max:20',
            'email'         => 'sometimes|nullable|email|max:255|unique:customers,email,' . $this->route('customers'),
            'address'       => 'sometimes|nullable|string|max:255',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return array_merge($rules, [
                'email' => 'sometimes|nullable|email|max:255|unique:customers,email,' . $this->route('customer'),
            ]);
        }

        return $rules;
    }
}
