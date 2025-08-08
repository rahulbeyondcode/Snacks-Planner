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
            'contact_number' => 'nullable|numeric|digits_between:10,12',
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
            'contact_number.numeric' => 'The contact number must be numeric.',
            'contact_number.digits_between' => 'The contact number must be between 10 and 12 digits.',
        ];
    }
}
