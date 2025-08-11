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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'group' => $this->whenLoaded('group', function () {
                return [
                    'group_id' => $this->group->group_id,
                    'name' => $this->group->name,
                ];
            }),
            'members' => $this->whenLoaded('subGroupMembers', function () {
                return $this->subGroupMembers->map(function ($member) {
                    return [
                        'sub_group_member_id' => $member->sub_group_member_id,
                        'user_id' => $member->user_id,
                        'user' => $member->user ? [
                            'user_id' => $member->user->user_id,
                            'name' => $member->user->name,
                            'email' => $member->user->email,
                        ] : null,
                    ];
                });
            }),
        ];
    }
}
