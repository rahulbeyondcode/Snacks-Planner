<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PaymentMethodResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'shop_id' => $this->shop_id,
            'name' => $this->name,
            'address' => $this->address,
            'contact_number' => $this->contact_number,
            'notes' => $this->notes,
            'payment_methods' => $this->whenLoaded('paymentMethods', function() {
                return $this->paymentMethods->pluck('payment_method')->toArray();
            }),
        ];

        return $data;
    }
}
