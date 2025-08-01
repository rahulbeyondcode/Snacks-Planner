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

    public function messages()
    {
        return [
            'money_pool_id.required' => trans('messages.moneypoolblock.money_pool_id_required'),
            'money_pool_id.integer' => trans('messages.moneypoolblock.money_pool_id_integer'),
            'money_pool_id.exists' => trans('messages.moneypoolblock.money_pool_id_exists'),
            'amount.required' => trans('messages.moneypoolblock.amount_required'),
            'amount.numeric' => trans('messages.moneypoolblock.amount_numeric'),
            'amount.min' => trans('messages.moneypoolblock.amount_min'),
            'amount.max' => trans('messages.moneypoolblock.amount_max'),
            'reason.required' => trans('messages.moneypoolblock.reason_required'),
            'reason.string' => trans('messages.moneypoolblock.reason_string'),
            'reason.max' => trans('messages.moneypoolblock.reason_max'),
            'block_date.required' => trans('messages.moneypoolblock.block_date_required'),
            'block_date.date' => trans('messages.moneypoolblock.block_date_date'),
            'block_date.after_or_equal' => trans('messages.moneypoolblock.block_date_after_or_equal'),
            'block_date.before_or_equal' => trans('messages.moneypoolblock.block_date_before_or_equal'),
            'block_id.required' => trans('messages.moneypoolblock.block_id_required'),
            'block_id.integer' => trans('messages.moneypoolblock.block_id_integer'),
            'block_id.exists' => trans('messages.moneypoolblock.block_id_exists'),
        ];
    }
}
