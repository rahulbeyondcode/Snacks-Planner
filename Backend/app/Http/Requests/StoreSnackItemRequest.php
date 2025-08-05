<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSnackItemRequest extends FormRequest
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
                Rule::unique('snack_items', 'name')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The snack item name is required.',
            'name.string' => 'The snack item name must be a string.',
            'name.max' => 'The snack item name may not be greater than 255 characters.',
            'name.unique' => 'A snack item with this name already exists.',
        ];
    }
}
