<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoneyPoolSettingsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->money_pool_setting_id,
            'per_month_amount' => (float) $this->per_month_amount,
            'multiplier' => (int) $this->multiplier,
        ];
    }
}
