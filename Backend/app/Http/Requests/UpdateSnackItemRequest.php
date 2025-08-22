<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSnackItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {        
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('snack_items', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('id'), 'snack_item_id'),
            ],
            'description' => 'sometimes|nullable|string|max:500',
            'price' => 'sometimes|nullable|numeric|min:0',
            'shop_id' => 'sometimes|integer|exists:shops,shop_id',
            'snack_price' => 'required_with:shop_id|numeric|min:0',
            'is_available' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'The snack item name must be a string.',
            'name.max' => 'The snack item name may not be greater than 255 characters.',
            'name.unique' => 'A snack item with this name already exists.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 500 characters.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
            'shop_id.integer' => 'The shop ID must be an integer.',
            'shop_id.exists' => 'The selected shop does not exist.',
            'snack_price.required_with' => 'The snack price is required when shop is selected.',
            'snack_price.numeric' => 'The snack price must be a number.',
            'snack_price.min' => 'The snack price must be at least 0.',
            'is_available.boolean' => 'The availability status must be true or false.',
        ];
    }
}
