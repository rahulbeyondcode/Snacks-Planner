<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMoneyPoolBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'reason' => 'required|string|max:500',
            'block_date' => 'required|date|after_or_equal:' . now()->startOfMonth()->format('Y-m-d') . '|before_or_equal:' . now()->endOfMonth()->format('Y-m-d'),
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => trans('money_pool_blocks.amount_required'),
            'amount.numeric' => trans('money_pool_blocks.amount_numeric'),
            'amount.min' => trans('money_pool_blocks.amount_min'),
            'amount.max' => trans('money_pool_blocks.amount_max'),
            'reason.required' => trans('money_pool_blocks.reason_required'),
            'reason.string' => trans('money_pool_blocks.reason_string'),
            'reason.max' => trans('money_pool_blocks.reason_max'),
            'block_date.required' => trans('money_pool_blocks.block_date_required'),
            'block_date.date' => trans('money_pool_blocks.block_date_date'),
            'block_date.after_or_equal' => trans('money_pool_blocks.block_date_after_or_equal'),
            'block_date.before_or_equal' => trans('money_pool_blocks.block_date_before_or_equal'),
        ];
    }
}
