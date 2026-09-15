<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectTeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'subject_id'    => $this->subject_id,
            'teacher_id'    => $this->teacher_id,
            'is_active'     => (bool) $this->is_active,
            'subject'       => new SubjectResource($this->whenLoaded('subject')),
            'teacher'       => new TeacherResource($this->whenLoaded('teacher')),
            'units'         => UnitResource::collection($this->whenLoaded('units')),
            'lessons'       => LessonResource::collection($this->whenLoaded('lessons')),
            'subscriptions' => SubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
