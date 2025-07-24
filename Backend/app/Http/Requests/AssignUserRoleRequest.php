<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignUserRoleRequest extends FormRequest
{
    public function authorize()
    {
        // Only account_manager is allowed; controller already checks, but can add further logic here if needed
        return true;
    }

    public function rules()
    {
        return [
            'role_id' => 'required|exists:roles,role_id',
        ];
    }
}
