<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\TranslatableResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationalStageResource extends JsonResource
{
    use TranslatableResource;

    public function toArray(Request $request): array
    {
        $locale = $this->resolveLocale($request);

        return [
            'id'                  => $this->id,
            'tenant_id'           => $this->tenant_id,
            'name'                => $this->translatable('name', $locale),
            'slug'                => $this->slug,
            'description'         => $this->translatable('description', $locale),
            'order'               => (int) $this->order,
            'is_active'           => (bool) $this->is_active,
            'grades_count'        => $this->whenCounted('grades'),
            'active_grades_count' => $this->whenNotNull($this->active_grades_count),
            'grades'              => GradeResource::collection($this->whenLoaded('grades')),
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
