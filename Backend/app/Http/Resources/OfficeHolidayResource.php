<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfficeHolidayResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->holiday_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'group_id' => $this->group_id,
            'holiday_date' => $this->holiday_date,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user'),
            'group' => $this->whenLoaded('group'),
        ];
    }
}