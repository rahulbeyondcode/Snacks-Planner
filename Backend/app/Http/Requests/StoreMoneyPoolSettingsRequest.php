<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMoneyPoolSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_month_amount' => 'required|numeric|min:1.00|max:1000.00',
            'multiplier' => 'required|integer|min:0|max:5',
        ];
    }

    public function messages()
    {
        return [
            'per_month_amount.required' => trans('money_pool_settings.per_month_amount_required'),
            'per_month_amount.numeric' => trans('money_pool_settings.per_month_amount_numeric'),
            'per_month_amount.min' => trans('money_pool_settings.per_month_amount_min'),
            'per_month_amount.max' => trans('money_pool_settings.per_month_amount_max'),
            'multiplier.required' => trans('money_pool_settings.multiplier_required'),
            'multiplier.integer' => trans('money_pool_settings.multiplier_integer'),
            'multiplier.min' => trans('money_pool_settings.multiplier_min'),
            'multiplier.max' => trans('money_pool_settings.multiplier_max'),
        ];
    }
}
