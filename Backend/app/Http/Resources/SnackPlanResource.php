<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SnackPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'snack_date' => $this->snack_date,
            'user_id' => $this->user_id,
            'total_amount' => $this->total_amount,
            'snack_items' => $this->snack_plan_details ? $this->snack_plan_details->map(function ($item) {
                return [
                    'snack_item_id' => $item->snack_item_id,
                    'shop_id' => $item->shop_id,
                    'quantity' => $item->quantity,
                    'category' => $item->category,
                    'price_per_item' => $item->price_per_item,
                    'total_price' => $item->total_price,
                    'payment_mode' => $item->payment_mode,
                    'discount' => $item->discount,
                    'delivery_charge' => $item->delivery_charge,
                    'upload_receipt' => $item->upload_receipt,
                ];
            }) : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
