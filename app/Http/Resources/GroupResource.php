<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subject = $this->relationLoaded('subject') ? $this->subject : $this->subject;
        $grade = $subject?->grade;
        $stage = $grade?->stage;

        return [
            'id'             => $this->id,
            'tenant_id'      => $this->tenant_id,
            'name'           => $this->name,
            'code'           => $this->code,
            'capacity'       => $this->capacity,
            'price'          => $this->price !== null ? (float) $this->price : null,
            'description'    => $this->description,
            'is_active'      => (bool) $this->is_active,
            'teacher'        => $this->whenLoaded('teacher', function () {
                return [
                    'id'     => $this->teacher->id,
                    'name'   => $this->teacher->name,
                    'title'  => $this->teacher->title,
                    'avatar' => $this->teacher->avatar,
                    'phone'  => $this->teacher->phone,
                ];
            }, $this->teacher ? [
                'id'     => $this->teacher->id,
                'name'   => $this->teacher->name,
                'title'  => $this->teacher->title,
                'avatar' => $this->teacher->avatar,
                'phone'  => $this->teacher->phone,
            ] : null),
            'subject'        => $subject ? [
                'id'          => $subject->id,
                'name'        => $subject->name,
                'code'        => $subject->code,
                'icon'        => $subject->icon,
                'color_theme' => $subject->color_theme,
            ] : null,
            'grade'          => $grade ? [
                'id'    => $grade->id,
                'name'  => $grade->name,
                'code'  => $grade->code,
                'stage' => $stage ? [
                    'id'   => $stage->id,
                    'name' => $stage->name,
                    'slug' => $stage->slug,
                ] : null,
            ] : null,
            'days'           => GroupDayResource::collection($this->whenLoaded('days', $this->days, $this->days)),
            'students_count' => $this->students_count ?? $this->students()->count(),
            'students'       => $this->whenLoaded('students', function () {
                return $this->students->map(function ($student) {
                    $joinedAt = $student->pivot?->joined_at ?? $student->pivot?->created_at;
                    if ($joinedAt && is_string($joinedAt)) {
                        $joinedAt = \Carbon\Carbon::parse($joinedAt)->toIso8601String();
                    } elseif ($joinedAt instanceof \DateTimeInterface) {
                        $joinedAt = $joinedAt->toIso8601String();
                    }

                    return [
                        'id'        => $student->id,
                        'name'      => $student->name,
                        'email'     => $student->email,
                        'phone'     => $student->phone,
                        'status'    => $student->pivot?->status ?? 'active',
                        'joined_at' => $joinedAt,
                        'notes'     => $student->pivot?->notes,
                    ];
                });
            }),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
