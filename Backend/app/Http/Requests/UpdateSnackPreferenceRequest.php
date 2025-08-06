<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSnackPreferenceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'preference' => [
                'required',
                'string',
                Rule::in([
                    'all_snacks',
                    'veg_only',
                    'no_snacks',
                    'veg_but_egg',
                    'no_beef',
                    'no_chicken'
                ])
            ]
        ];
    }

    public function messages()
    {
        return [
            'preference.required' => 'Snack preference is required.',
            'preference.in' => 'Invalid snack preference selected. Please choose from the available options.'
        ];
    }
}
