<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LessonAttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'lesson_id'  => $this->lesson_id,
            'title'      => $this->title,
            'file_url'   => $this->file_path ? (str_starts_with($this->file_path, 'http') ? $this->file_path : Storage::disk('public')->url($this->file_path)) : null,
            'file_type'  => $this->file_type,
            'file_size'  => $this->file_size,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
