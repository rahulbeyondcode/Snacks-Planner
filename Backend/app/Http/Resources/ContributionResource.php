<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContributionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'contribution_id' => $this->contribution_id,
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->name : null,
            'status' => $this->status === 'paid',
        ];
    }
}
