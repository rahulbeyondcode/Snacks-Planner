<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockMoneyPoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'money_pool_id' => 'required|integer|exists:money_pools,money_pool_id',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'reason' => 'required|string|max:500',
            'block_date' => 'required|date|after_or_equal:'.now()->startOfMonth()->format('Y-m-d').'|before_or_equal:'.now()->endOfMonth()->format('Y-m-d'),
        ];

        // If updating (block_id is provided), make it required and exists
        if ($this->has('block_id')) {
            $rules['block_id'] = 'required|integer|exists:money_pool_blocks,block_id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'money_pool_id.required' => trans('money_pool_blocks.money_pool_id_required'),
            'money_pool_id.integer' => trans('money_pool_blocks.money_pool_id_integer'),
            'money_pool_id.exists' => trans('money_pool_blocks.money_pool_id_exists'),
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
            'block_id.required' => trans('money_pool_blocks.block_id_required'),
            'block_id.integer' => trans('money_pool_blocks.block_id_integer'),
            'block_id.exists' => trans('money_pool_blocks.block_id_exists'),
        ];
    }
}
