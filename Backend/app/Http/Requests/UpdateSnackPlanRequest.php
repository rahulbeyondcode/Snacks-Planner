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
            'snack_date' => 'sometimes|date_format:d-m-Y',
            'total_amount' => 'sometimes|numeric',
            'snack_items' => 'sometimes|array|min:1',
            'snack_items.*.snack_item_id' => 'required|integer|exists:snack_items,snack_item_id',
            'snack_items.*.shop_id' => 'required|integer|exists:shops,shop_id',
            'snack_items.*.quantity' => 'required|integer|min:1',
            'snack_items.*.category_id' => 'required|integer|exists:categories,id',
            'snack_items.*.price_per_item' => 'required|numeric',
            'snack_items.*.total_price' => 'required|numeric',
            'snack_items.*.payment_mode' => 'required|in:cash,card,upi,wallet',
            'snack_items.*.discount' => 'nullable|numeric',
            'snack_items.*.delivery_charge' => 'nullable|numeric',
            'snack_items.*.upload_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'snack_date.date_format' => 'The snack date must be in DD-MM-YYYY format.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'snack_items.array' => 'The snack items must be an array.',
            'snack_items.min' => 'At least one snack item is required.',
            'snack_items.*.snack_item_id.required' => 'Each snack item must have a snack item ID.',
            'snack_items.*.snack_item_id.exists' => 'The selected snack item does not exist.',
            'snack_items.*.shop_id.required' => 'Each snack item must have a shop ID.',
            'snack_items.*.shop_id.exists' => 'The selected shop does not exist.',
            'snack_items.*.quantity.required' => 'Each snack item must have a quantity.',
            'snack_items.*.quantity.min' => 'The quantity must be at least 1.',
            'snack_items.*.category_id.required' => 'Each snack item must have a category ID.',
            'snack_items.*.category_id.exists' => 'The selected category does not exist.',
            'snack_items.*.price_per_item.required' => 'Each snack item must have a price per item.',
            'snack_items.*.total_price.required' => 'Each snack item must have a total price.',
            'snack_items.*.payment_mode.required' => 'Each snack item must have a payment mode.',
            'snack_items.*.payment_mode.in' => 'The payment mode must be one of: cash, card, upi, wallet.',
        ];
    }
}
