<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSnackPlanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plan_date' => 'sometimes|date',
            'group_id' => 'sometimes|integer|exists:groups,id',
            'snack_type' => 'sometimes|string|max:32',
            'shop_id' => 'sometimes|integer|exists:shops,id',
            'discount' => 'nullable|numeric|min:0',
            'delivery_charge' => 'nullable|numeric|min:0',
            'payment_method' => 'sometimes|string|max:32',
        ];
    }
}
