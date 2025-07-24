<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContributionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'month' => $this->month,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
