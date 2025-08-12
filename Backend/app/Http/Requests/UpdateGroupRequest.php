<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'employees' => 'required|array',
            'snack_managers' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The group name is required.',
            'name.string' => 'The group name must be a string.',
            'name.max' => 'The group name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'employees.required' => 'Employees are required.',
            'employees.array' => 'Employees must be an array.',
            'snack_managers.required' => 'Snack managers are required.',
            'snack_managers.array' => 'Snack managers must be an array.',
        ];
    }
} 