<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'unit_id'            => $this->unit_id,
            'teacher_id'         => $this->teacher_id,
            'subject_id'         => $this->subject_id,
            'subject_teacher_id' => $this->subject_teacher_id,
            'title'              => $this->title,
            'description'        => $this->description,
            'video_url'          => $this->video_url,
            'duration'           => (int) $this->duration,
            'sort_order'         => (int) $this->sort_order,
            'is_free'            => (bool) $this->is_free,
            'is_active'          => (bool) $this->is_active,
            'attachments'        => LessonAttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
