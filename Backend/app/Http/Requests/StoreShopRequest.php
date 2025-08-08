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
            'contact' => 'nullable|string|max:64',
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
            'contact.string' => 'The shop contact must be a string.',
            'contact.max' => 'The shop contact may not be greater than 64 characters.',
        ];
    }
}
