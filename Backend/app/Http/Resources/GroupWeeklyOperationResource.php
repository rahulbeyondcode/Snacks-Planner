<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupWeeklyOperationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->group_weekly_operation_id,
            'group_id' => $this->group_id,
            'week_start_date' => $this->week_start_date,
            'employee_id' => $this->employee_id,
            'assigned_by' => $this->assigned_by,
            'created_at' => $this->created_at,
            'group' => $this->whenLoaded('group'),
            'employee' => $this->whenLoaded('employee'),
            'assigned_by_user' => $this->whenLoaded('assignedBy'),
            'details' => GroupWeeklyOperationDetailResource::collection($this->whenLoaded('details')),
        ];
    }
}
