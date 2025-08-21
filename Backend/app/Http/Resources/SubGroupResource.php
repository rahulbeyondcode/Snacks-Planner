<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'sub_group_id' => $this->sub_group_id,
            'group_id' => $this->group_id,
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'members' => $this->whenLoaded('subGroupMembers', function () {
                return $this->subGroupMembers->pluck('user_id')->toArray();
            }),
        ];
    }
}
