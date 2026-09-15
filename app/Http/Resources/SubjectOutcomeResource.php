<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\TranslatableResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectOutcomeResource extends JsonResource
{
    use TranslatableResource;

    public function toArray(Request $request): array
    {
        $locale = $this->resolveLocale($request);

        return [
            'id'         => $this->id,
            'subject_id' => $this->subject_id,
            'title'      => $this->translatable('title', $locale),
            'order'      => (int) $this->order,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
