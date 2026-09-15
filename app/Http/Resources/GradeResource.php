<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\TranslatableResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    use TranslatableResource;

    public function toArray(Request $request): array
    {
        $locale = $this->resolveLocale($request);

        return [
            'id'             => $this->id,
            'tenant_id'      => $this->tenant_id,
            'stage_id'       => $this->stage_id,
            'name'           => $this->translatable('name', $locale),
            'slug'           => $this->slug,
            'code'           => $this->code,
            'order'          => (int) $this->order,
            'is_active'      => (bool) $this->is_active,
            'subjects_count' => $this->whenCounted('subjects'),
            'students_count' => $this->whenCounted('students'),
            'stage'          => new EducationalStageResource($this->whenLoaded('stage')),
            'subjects'       => SubjectResource::collection($this->whenLoaded('subjects')),
            'students'       => StudentResource::collection($this->whenLoaded('students')),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
