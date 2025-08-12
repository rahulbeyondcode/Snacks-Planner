<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_groups', 'name')->where(function ($query) {
                    return $query->where('group_id', $this->group_id)->whereNull('deleted_at');
                }),
            ],
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:active,inactive',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,user_id',
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.required' => 'Group is required.',
            'group_id.exists' => 'Selected group does not exist.',
            'name.required' => 'Sub group name is required.',
            'name.unique' => 'A sub group with this name already exists in the selected group.',
            'start_date.required' => 'Start date is required.',
            'start_date.after_or_equal' => 'Start date must be today or a future date.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'status.in' => 'Status must be either active or inactive.',
            'members.array' => 'Members must be an array.',
            'members.*.exists' => 'One or more selected users do not exist.',
        ];
    }
}
