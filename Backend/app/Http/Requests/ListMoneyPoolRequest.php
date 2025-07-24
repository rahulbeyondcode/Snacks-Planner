<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListMoneyPoolRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'created_by' => 'nullable|integer|exists:users,user_id',
        ];
    }
}
