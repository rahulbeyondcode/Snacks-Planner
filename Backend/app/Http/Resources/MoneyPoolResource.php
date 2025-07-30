<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoneyPoolResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'money_pool_id' => $this->money_pool_id,
            'total_collected_amount' => (float) $this->total_collected_amount,
            'employer_contribution' => (float) $this->employer_contribution,
            'total_pool_amount' => (float) $this->total_pool_amount,
            'blocked_amount' => (float) $this->blocked_amount,
            'total_available_amount' => (float) $this->total_available_amount,
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->user_id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),
            'settings' => $this->whenLoaded('settings', function () {
                return [
                    'id' => $this->settings->money_pool_setting_id,
                    'per_month_amount' => (float) $this->settings->per_month_amount,
                    'multiplier' => (int) $this->settings->multiplier,
                ];
            }),
        ];
    }
}
