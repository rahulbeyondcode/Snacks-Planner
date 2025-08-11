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
            'contact_number' => 'nullable|numeric|digits_between:10,12',
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
            'contact_number.numeric' => 'The contact number must be numeric.',
            'contact_number.digits_between' => 'The contact number must be between 10 and 12 digits.',
        ];
    }
}
