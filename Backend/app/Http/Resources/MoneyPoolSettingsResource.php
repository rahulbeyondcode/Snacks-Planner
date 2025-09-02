<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoneyPoolSettingsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'money_pool_setting_id' => $this->money_pool_setting_id,
            'per_month_amount' => (float) $this->per_month_amount,
            'multiplier' => (int) $this->multiplier,
            'total_users' => $this->total_active_users,
        ];
    }
}
