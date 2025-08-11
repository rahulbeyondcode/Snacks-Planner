<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupRequest extends FormRequest
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
                Rule::unique('groups', 'name')->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string|max:255',
            'employees' => 'required|array',
            'snack_managers' => 'required|array',
            'sort_order' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The group name is required.',
            'name.string' => 'The group name must be a string.',
            'name.max' => 'The group name may not be greater than 255 characters.',
            'name.unique' => 'A group with this name already exists.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'employees.required' => 'Employees are required.',
            'employees.array' => 'Employees must be an array.',
            'snack_managers.required' => 'Snack managers are required.',
            'snack_managers.array' => 'Snack managers must be an array.',
            'sort_order.integer' => 'Sort order must be an integer.',
        ];
    }
} 