<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\TranslatableResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeacherResource extends JsonResource
{
    use TranslatableResource;

    public function toArray(Request $request): array
    {
        $locale = $this->resolveLocale($request);

        return [
            'id'               => $this->id,
            'tenant_id'        => $this->tenant_id,
            'user_id'          => $this->user_id,
            'name'             => $this->translatable('name', $locale),
            'title'            => $this->translatable('title', $locale),
            'email'            => $this->email,
            'phone'            => $this->phone,
            'address'          => $this->address,
            'avatar'           => $this->avatar ? (str_starts_with($this->avatar, 'http') ? $this->avatar : Storage::disk('public')->url($this->avatar)) : null,
            'bio'              => $this->translatable('bio', $locale),
            'experience_years' => (int) $this->experience_years,
            'students_count'   => (int) $this->students_count,
            'rating'           => (float) $this->rating,
            'whatsapp_number'  => $this->whatsapp_number,
            'color_theme'      => $this->color_theme,
            'is_active'        => (bool) ($this->pivot?->is_active ?? $this->is_active),
            'features'         => TeacherFeatureResource::collection($this->whenLoaded('features')),
            'subjects'         => SubjectResource::collection($this->whenLoaded('subjects')),
            'assignments'      => SubjectTeacherResource::collection($this->whenLoaded('assignments')),
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
        ];
    }
}
