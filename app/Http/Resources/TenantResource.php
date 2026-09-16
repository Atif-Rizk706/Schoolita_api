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
            'logo'                    => $this->profile?->logo ? (str_starts_with($this->profile->logo, 'http') ? $this->profile->logo : Storage::disk('public')->url($this->profile->logo)) : null,
            'cover_image'             => $this->profile?->cover_image ? (str_starts_with($this->profile->cover_image, 'http') ? $this->profile->cover_image : Storage::disk('public')->url($this->profile->cover_image)) : null,
            'primary_color'           => $this->profile?->primary_color,
            'secondary_color'         => $this->profile?->secondary_color,
            'phone'                   => $this->phone,
            'whatsapp'                => $this->profile?->whatsapp,
            'email'                   => $this->email,
            'is_active'               => (bool) $this->is_active,
            'subscription_plan'       => $this->subscription_plan,
            'subscription_expires_at' => $this->subscription_expires_at?->toIso8601String(),
            'profile'                 => $this->profile ? new TenantProfileResource($this->profile) : null,
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
