<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignWeeklyOperationRequest extends FormRequest
{
    public function authorize()
    {
        // Only operations_manager can assign
        return $this->user() && $this->user()->role && $this->user()->role->name === 'operations_manager';
    }

    public function rules()
    {
        return [
            'group_id' => 'required|integer|exists:groups,group_id',
            'week_start_date' => 'required|date',
            'employee_id' => 'required|integer|exists:users,user_id',
            'assigned_by' => 'required|integer|exists:users,user_id',
            'details' => 'nullable|array',
            'details.*.task_description' => 'required_with:details|string',
        ];
    }
}
