<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        // Only account_manager is allowed; controller already checks, but can add further logic here if needed
        return true;
    }

    public function rules()
    {
        $userId = $this->route('id');
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId . ',user_id',
            'password' => 'nullable|string|min:8',

        ];
    }
}
