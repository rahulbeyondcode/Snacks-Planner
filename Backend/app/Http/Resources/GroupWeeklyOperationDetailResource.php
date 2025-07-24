<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupWeeklyOperationDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->group_weekly_operation_detail_id,
            'group_weekly_operation_id' => $this->group_weekly_operation_id,
            'task_description' => $this->task_description,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
