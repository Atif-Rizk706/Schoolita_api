<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'slug'                    => $this->slug,
            'domain'                  => $this->domain,
            'logo'                    => $this->logo ? (str_starts_with($this->logo, 'http') ? $this->logo : Storage::disk('public')->url($this->logo)) : null,
            'cover_image'             => $this->cover_image ? (str_starts_with($this->cover_image, 'http') ? $this->cover_image : Storage::disk('public')->url($this->cover_image)) : null,
            'primary_color'           => $this->primary_color,
            'secondary_color'         => $this->secondary_color,
            'phone'                   => $this->phone,
            'whatsapp'                => $this->whatsapp,
            'email'                   => $this->email,
            'address'                 => $this->address,
            'about_us'                => $this->about_us,
            'vision'                  => $this->vision,
            'mission'                 => $this->mission,
            'working_hours'           => $this->working_hours,
            'social_links'            => $this->social_links ?? [],
            'hero_title'              => $this->hero_title,
            'hero_subtitle'           => $this->hero_subtitle,
            'stats'                   => $this->stats ?? [],
            'is_active'               => (bool) $this->is_active,
            'subscription_plan'       => $this->subscription_plan,
            'subscription_expires_at' => $this->subscription_expires_at?->toIso8601String(),
            'stages'                  => EducationalStageResource::collection($this->whenLoaded('stages')),
            'grades'                  => GradeResource::collection($this->whenLoaded('grades')),
            'subjects'                => SubjectResource::collection($this->whenLoaded('subjects')),
            'teachers'                => TeacherResource::collection($this->whenLoaded('teachers')),
            'testimonials'            => TestimonialResource::collection($this->whenLoaded('testimonials')),
            'created_at'              => $this->created_at?->toIso8601String(),
            'updated_at'              => $this->updated_at?->toIso8601String(),
        ];
    }
}
