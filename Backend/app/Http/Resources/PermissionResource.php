<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'permission_id' => $this->permission_id,
            'module' => $this->module,
            'action' => $this->action,
            'resource' => $this->getAttribute('resource'),
            'description' => $this->description,
            'identifier' => $this->identifier,
            'roles' => $this->whenLoaded('roles', function () {
                return RoleResource::collection($this->roles);
            }),
        ];
    }
}
