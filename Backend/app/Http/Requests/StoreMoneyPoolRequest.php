<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMoneyPoolRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_month_amount' => 'required|numeric|min:0',
            'multiplier' => 'required|numeric|min:1',
            'created_by' => 'required|integer|exists:users,user_id',
        ];
    }
}
