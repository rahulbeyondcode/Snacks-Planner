<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShopRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shops', 'name')->whereNull('deleted_at'),
            ],
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'required|string|in:cash,bank_transfer,card,upi'
        ];
    }    

    public function messages()
    {
        return [
            'name.required' => 'The shop name is required.',
            'name.string' => 'The shop name must be a string.',
            'name.max' => 'The shop name may not be greater than 255 characters.',
            'name.unique' => 'A shop with this name already exists.',
            'address.required' => 'The shop address is required.',
            'address.string' => 'The shop address must be a string.',
            'address.max' => 'The shop address may not be greater than 255 characters.',
            'contact_number.string' => 'The contact number must be a string.',            
            'notes.string' => 'The notes must be a string.',
            'notes.max' => 'The notes may not be greater than 1000 characters.',
            'payment_methods.array' => 'Payment methods must be an array.',
            'payment_methods.*.required' => 'Payment method is required.',
            'payment_methods.*.string' => 'Payment method must be a string.',
            'payment_methods.*.in' => 'Payment method must be one of: cash, bank_transfer, card, upi.'
        ];
    }
}
