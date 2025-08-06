<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSnackPlanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'snack_date' => 'required',
            'user_id' => 'required|integer',
            'total_amount' => 'required|numeric',
            'snack_items' => 'required|array|min:1',
            'snack_items.*.snack_item_id' => 'required|integer|exists:snack_items,snack_item_id',
            'snack_items.*.shop_id' => 'required|integer|exists:shops,shop_id',
            'snack_items.*.quantity' => 'required|integer|min:1',
            'snack_items.*.category' => 'required|in:veg,non-veg,chicken-only',
            'snack_items.*.price_per_item' => 'required|numeric',
            'snack_items.*.total_price' => 'required|numeric',
            'snack_items.*.payment_mode' => 'required|in:cash,card,upi,wallet',
            'snack_items.*.discount' => 'nullable|numeric',
            'snack_items.*.delivery_charge' => 'nullable|numeric',
            'snack_items.*.upload_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
