<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoneyPoolBlockResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'block_id' => $this->block_id,
            'money_pool_id' => $this->money_pool_id,
            'amount' => (float) $this->amount,
            'reason' => $this->reason,
            'block_date' => $this->block_date,
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->user_id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),
        ];
    }
}
