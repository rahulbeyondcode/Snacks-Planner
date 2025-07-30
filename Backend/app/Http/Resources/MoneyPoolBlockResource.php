<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoneyPoolBlockResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'block_id' => $this->block_id,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'block_date' => $this->block_date,
        ];
    }
}
