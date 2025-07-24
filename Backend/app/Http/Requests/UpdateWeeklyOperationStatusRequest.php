<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWeeklyOperationStatusRequest extends FormRequest
{
    public function authorize()
    {
        // Only assigned employee can update status
        return $this->user() && $this->user()->role && in_array($this->user()->role->name, ['operations_staff', 'operations_manager']);
    }

    public function rules()
    {
        return [
            'status' => 'required|string|in:pending,in_progress,completed',
            'detail_id' => 'required|integer|exists:group_weekly_operation_details,group_weekly_operation_detail_id',
        ];
    }
}
