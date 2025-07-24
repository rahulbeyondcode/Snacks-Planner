<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoneyPoolResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'per_month_amount' => $this->per_month_amount,
            'multiplier' => $this->multiplier,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Add related blocks or summary if needed
        ];
    }
}
