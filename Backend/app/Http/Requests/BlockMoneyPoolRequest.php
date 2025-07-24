<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockMoneyPoolRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
            'block_date' => 'required|date',
            'created_by' => 'required|integer|exists:users,user_id',
        ];
    }
}
