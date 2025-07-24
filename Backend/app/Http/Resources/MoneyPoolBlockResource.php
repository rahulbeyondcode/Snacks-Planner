<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoneyPoolBlockResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'money_pool_id' => $this->money_pool_id,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'block_date' => $this->block_date,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
