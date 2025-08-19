<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfficeHolidayResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->holiday_id,
            // 'user_id' => $this->user_id,
            // 'type' => $this->type,
            // 'group_id' => $this->group_id,
            'date' => $this->holiday_date,
            'name' => $this->description,
            // 'user' => $this->whenLoaded('user'),
            // 'group' => $this->whenLoaded('group'),
        ];
    }
}
