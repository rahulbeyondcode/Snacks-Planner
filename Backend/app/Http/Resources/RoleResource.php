<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'role_id' => $this->getAttribute('role_id'),
            'name' => $this->getAttribute('name'),
            'description' => $this->getAttribute('description'),
            // Exclude created_at, updated_at, deleted_at
            // Also exclude pivot data with timestamps by default
            'pivot' => $this->when($this->getAttribute('pivot'), [
                'permission_id' => $this->getAttribute('pivot')?->permission_id,
                'role_id' => $this->getAttribute('pivot')?->role_id,
                'is_active' => $this->getAttribute('pivot')?->is_active,
                // Exclude pivot created_at, updated_at
            ]),
        ];
    }
}
