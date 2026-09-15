<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'tenant_id'          => $this->tenant_id,
            'subject_teacher_id' => $this->subject_teacher_id,
            'subject_id'         => $this->subject_id,
            'teacher_id'         => $this->teacher_id,
            'title'              => $this->title,
            'description'        => $this->description,
            'sort_order'         => (int) $this->sort_order,
            'is_active'          => (bool) $this->is_active,
            'lessons'            => LessonResource::collection($this->whenLoaded('lessons')),
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
