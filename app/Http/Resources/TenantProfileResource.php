<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\TranslatableResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TenantProfileResource extends JsonResource
{
    use TranslatableResource;

    public function toArray(Request $request): array
    {
        $locale = $this->resolveLocale($request);

        return [
            'id'              => $this->id,
            'logo'            => $this->logo ? (str_starts_with($this->logo, 'http') ? $this->logo : Storage::disk('public')->url($this->logo)) : null,
            'cover_image'     => $this->cover_image ? (str_starts_with($this->cover_image, 'http') ? $this->cover_image : Storage::disk('public')->url($this->cover_image)) : null,
            'primary_color'   => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'whatsapp'        => $this->whatsapp,
            'address'         => $this->translatable('address', $locale),
            'working_hours'   => $this->translatable('working_hours', $locale),
            'about_us'        => $this->translatable('about_us', $locale),
            'vision'          => $this->translatable('vision', $locale),
            'mission'         => $this->translatable('mission', $locale),
            'hero_title'      => $this->translatable('hero_title', $locale),
            'hero_subtitle'   => $this->translatable('hero_subtitle', $locale),
            'social_links'    => $this->social_links ?? [],
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
