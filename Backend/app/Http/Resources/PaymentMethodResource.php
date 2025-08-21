<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'payment_method_id' => $this->payment_method_id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        // Include pivot data if this is a relationship
        if ($this->pivot) {
            $data['shop_payment_method'] = [
                'is_active' => $this->pivot->is_active,
                'additional_details' => $this->pivot->additional_details,
                'created_at' => $this->pivot->created_at,
                'updated_at' => $this->pivot->updated_at,
            ];
        }

        return $data;
    }
} 