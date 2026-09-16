<?php

namespace App\Services\Tenant;

use App\Models\Group;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TenantGroupService
{
    /**
     * Get paginated or filtered list of groups for a tenant.
     */
    public function getGroups(Tenant $tenant, Request $request)
    {
        $query = Group::with(['teacher', 'subject.grade.stage', 'days'])
            ->withCount('students')
            ->where('tenant_id', $tenant->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('grade_id')) {
            $gradeId = $request->grade_id;
            $query->whereHas('subject', function ($q) use ($gradeId) {
                $q->where('grade_id', $gradeId);
            });
        }

        if ($request->filled('day')) {
            $day = $request->day;
            $query->whereHas('days', function ($q) use ($day) {
                $q->where('day_of_week', $day);
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        return $query->latest()->paginate($request->input('per_page', 15));
    }

    /**
     * Store new group with schedule days and optional students.
     */
    public function storeGroup(Tenant $tenant, array $data): Group
    {
        return DB::transaction(function () use ($tenant, $data) {
            $days = $data['days'] ?? [];
            $studentIds = $data['student_ids'] ?? [];

            unset($data['days'], $data['student_ids']);

            $data['tenant_id'] = $tenant->id;
            $group = Group::create($data);

            if (!empty($days)) {
                foreach ($days as $dayData) {
                    $group->days()->create([
                        'day_of_week' => $dayData['day_of_week'],
                        'start_time'  => $dayData['start_time'],
                        'end_time'    => $dayData['end_time'],
                        'room'        => $dayData['room'] ?? null,
                    ]);
                }
            }

            if (!empty($studentIds)) {
                $syncData = [];
                foreach ($studentIds as $id) {
                    $syncData[$id] = [
                        'status'    => 'active',
                        'joined_at' => now(),
                    ];
                }
                $group->students()->sync($syncData);
            }

            return $group->load(['teacher', 'subject.grade.stage', 'days', 'students']);
        });
    }

    /**
     * Update existing group and sync schedule days.
     */
    public function updateGroup(Group $group, array $data): Group
    {
        return DB::transaction(function () use ($group, $data) {
            $days = $data['days'] ?? null;
            $studentIds = $data['student_ids'] ?? null;

            unset($data['days'], $data['student_ids']);

            $group->update($data);

            if ($days !== null) {
                $group->days()->delete();
                foreach ($days as $dayData) {
                    $group->days()->create([
                        'day_of_week' => $dayData['day_of_week'],
                        'start_time'  => $dayData['start_time'],
                        'end_time'    => $dayData['end_time'],
                        'room'        => $dayData['room'] ?? null,
                    ]);
                }
            }

            if ($studentIds !== null) {
                $group->students()->sync($studentIds);
            }

            return $group->fresh(['teacher', 'subject.grade.stage', 'days', 'students']);
        });
    }

    /**
     * Delete group.
     */
    public function deleteGroup(Group $group): bool
    {
        return $group->delete();
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Group $group): Group
    {
        $group->update(['is_active' => !$group->is_active]);
        return $group->fresh(['teacher', 'subject.grade.stage', 'days']);
    }

    /**
     * Add student to group (group_users).
     */
    public function addStudent(Group $group, int $userId, array $attributes = []): Group
    {
        $student = User::where('tenant_id', $group->tenant_id)->findOrFail($userId);

        if ($group->capacity && $group->students()->count() >= $group->capacity) {
            throw ValidationException::withMessages([
                'capacity' => ['عذراً، اكتمل العدد الأقصى لطلاب هذه المجموعة / Group capacity reached.'],
            ]);
        }

        $group->students()->syncWithoutDetaching([
            $student->id => [
                'status'    => $attributes['status'] ?? 'active',
                'joined_at' => $attributes['joined_at'] ?? now(),
                'notes'     => $attributes['notes'] ?? null,
            ],
        ]);

        return $group->fresh(['teacher', 'subject.grade.stage', 'days', 'students']);
    }

    /**
     * Remove student from group.
     */
    public function removeStudent(Group $group, int $userId): Group
    {
        $group->students()->detach($userId);
        return $group->fresh(['teacher', 'subject.grade.stage', 'days', 'students']);
    }
}
