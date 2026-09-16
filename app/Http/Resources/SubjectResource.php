<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\TranslatableResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SubjectResource extends JsonResource
{
    use TranslatableResource;

    public function toArray(Request $request): array
    {
        $locale = $this->resolveLocale($request);

        return [
            'id'                 => $this->id,
            'tenant_id'          => $this->tenant_id,
            'grade_id'           => $this->grade_id,
            'name'               => $this->translatable('name', $locale),
            'slug'               => $this->slug,
            'code'               => $this->code,
            'description'        => $this->translatable('description', $locale),
            'bio'                => $this->translatable('bio', $locale),
            'icon'               => $this->icon ? (str_starts_with($this->icon, 'http') ? $this->icon : Storage::disk('public')->url($this->icon)) : null,
            'color_theme'        => $this->color_theme,
            'lessons_count'      => (int) $this->lessons_count,
            'subscription_price' => (float) $this->subscription_price,
            'is_active'          => (bool) ($this->pivot?->is_active ?? $this->is_active),
            'grade'              => new GradeResource($this->whenLoaded('grade')),
            'teachers'           => TeacherResource::collection($this->whenLoaded('teachers')),
            'outcomes'           => SubjectOutcomeResource::collection($this->whenLoaded('outcomes')),
            'features'           => SubjectFeatureResource::collection($this->whenLoaded('features')),
            'units'              => UnitResource::collection($this->whenLoaded('units')),
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}

