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
                    ->ignore($this->route('snack_item'), 'snack_item_id'),
            ],
            'type' => 'sometimes|string|max:32',
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'The snack item name must be a string.',
            'name.max' => 'The snack item name may not be greater than 255 characters.',
            'name.unique' => 'A snack item with this name already exists.',
            'type.string' => 'The snack item type must be a string.',
            'type.max' => 'The snack item type may not be greater than 32 characters.',
        ];
    }
}
