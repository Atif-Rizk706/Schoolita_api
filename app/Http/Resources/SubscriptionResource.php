<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'tenant_id'          => $this->tenant_id,
            'user_id'            => $this->user_id,
            'subject_teacher_id' => $this->subject_teacher_id,
            'price'              => (float) $this->price,
            'currency'           => $this->currency,
            'payment_method'     => $this->payment_method,
            'status'             => $this->status,
            'starts_at'          => $this->starts_at?->format('Y-m-d'),
            'ends_at'            => $this->ends_at?->format('Y-m-d'),
            'notes'              => $this->notes,
            'user'               => new StudentResource($this->whenLoaded('user')),
            'subject_teacher'    => new SubjectTeacherResource($this->whenLoaded('subjectTeacher')),
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
