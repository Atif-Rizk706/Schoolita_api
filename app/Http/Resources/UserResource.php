<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'tenant_id'       => $this->tenant_id,
            'name'            => $this->name,
            'email'           => $this->email,
            'role'            => $this->role,
            'is_active'       => (bool) $this->is_active,
            'image'           => $this->image ? (str_starts_with($this->image, 'http') ? $this->image : Storage::disk('public')->url($this->image)) : null,
            'phone'           => $this->phone,
            'whatsapp'        => $this->whatsapp,
            'parent_email'    => $this->parent_email,
            'parent_phone'    => $this->parent_phone,
            'parent_whatsapp' => $this->parent_whatsapp,
            'country'         => $this->country,
            'grade_id'        => $this->grade_id,
            'birth_date'      => $this->birth_date?->format('Y-m-d'),
            'tenant'          => new TenantResource($this->whenLoaded('tenant')),
            'grade'           => new GradeResource($this->whenLoaded('grade')),
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
