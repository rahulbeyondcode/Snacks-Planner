<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShopRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $shopId = $this->route('shop'); // Get the shop ID from the route parameter
        
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('shops', 'name')
                    ->ignore($shopId, 'shop_id')
                    ->whereNull('deleted_at'),
            ],
            'address' => 'sometimes|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'required|string|in:cash,bank_transfer,card,upi'
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'The shop name must be a string.',
            'name.max' => 'The shop name may not be greater than 255 characters.',
            'name.unique' => 'A shop with this name already exists.',
            'address.string' => 'The shop address must be a string.',
            'address.max' => 'The shop address may not be greater than 255 characters.',
            'contact_number.string' => 'The contact number must be a string.',
            'contact_number.max' => 'The contact number may not be greater than 20 characters.',
            'notes.string' => 'The notes must be a string.',
            'notes.max' => 'The notes may not be greater than 1000 characters.',
            'payment_methods.array' => 'Payment methods must be a string.',
            'payment_methods.*.required' => 'Payment method is required.',
            'payment_methods.*.string' => 'Payment method must be a string.',
            'payment_methods.*.in' => 'Payment method must be one of: cash, bank_transfer, card, upi.'
        ];
    }
}
