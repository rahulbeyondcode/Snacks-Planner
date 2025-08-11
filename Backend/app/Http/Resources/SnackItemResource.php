<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SnackItemResource extends JsonResource
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
            'snack_item_id' => $this->snack_item_id,
            'name' => $this->name,
            'description' => $this->description,
            'shop_mappings' => $this->shopMappings->map(function($mapping) {
                return [
                    'shop_id' => $mapping->shop_id,
                    'shop_name' => $mapping->shop->name,
                    'snack_price' => $mapping->snack_price,
                    'is_available' => $mapping->is_available,
                ];
            }),
        ];
    }
}
