<?php

namespace App\Services\Tenant;

use App\Models\Teacher;
use App\Models\Tenant;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;

class TenantTeacherService
{
    use FileUploadTrait;

    /**
     * Get list of teachers for current tenant.
     */
    public function getTeachers(Tenant $tenant, Request $request)
    {
        $query = Teacher::with(['subjects.grade.stage', 'features'])
            ->where('tenant_id', $tenant->id);

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($request->input('per_page', 15));
    }

    /**
     * Store new teacher.
     */
    public function storeTeacher(Tenant $tenant, array $data): Teacher
    {
        $data['tenant_id'] = $tenant->id;

        $subjects = $data['subject_ids'] ?? [];
        unset($data['subject_ids']);

        $features = $data['features'] ?? [];
        unset($data['features']);

        if (isset($data['avatar'])) {
            $data['avatar'] = $this->uploadFile($data['avatar'], 'teachers');
        }

        $teacher = Teacher::create($data);

        if (!empty($subjects)) {
            $teacher->subjects()->sync($subjects);
        }

        if (!empty($features)) {
            foreach ($features as $index => $featureItem) {
                $title = is_array($featureItem) ? ($featureItem['title'] ?? $featureItem) : $featureItem;
                $teacher->features()->create([
                    'title' => $title,
                    'order' => $index,
                ]);
            }
        }

        return $teacher->load(['subjects.grade.stage', 'features']);
    }

    /**
     * Update teacher.
     */
    public function updateTeacher(Teacher $teacher, array $data): Teacher
    {
        if (isset($data['subject_ids'])) {
            $teacher->subjects()->sync($data['subject_ids']);
            unset($data['subject_ids']);
        }

        if (isset($data['features'])) {
            $features = $data['features'];
            unset($data['features']);

            $teacher->features()->delete();
            foreach ($features as $index => $featureItem) {
                $title = is_array($featureItem) ? ($featureItem['title'] ?? $featureItem) : $featureItem;
                $teacher->features()->create([
                    'title' => $title,
                    'order' => $index,
                ]);
            }
        }

        if (array_key_exists('avatar', $data)) {
            $data['avatar'] = $this->uploadFile($data['avatar'], 'teachers', $teacher->avatar);
        }

        $teacher->update($data);

        return $teacher->load(['subjects.grade.stage', 'features']);
    }

    /**
     * Delete teacher avatar file and reset avatar field to null.
     */
    public function deleteAvatar(Teacher $teacher): Teacher
    {
        if ($teacher->avatar) {
            $this->deleteFile($teacher->avatar);
            $teacher->update(['avatar' => null]);
        }

        return $teacher->fresh(['subjects.grade.stage', 'features']);
    }

    /**
     * Toggle teacher active status (is_active).
     */
    public function toggleStatus(Teacher $teacher): Teacher
    {
        $teacher->update(['is_active' => !$teacher->is_active]);

        return $teacher->fresh(['subjects.grade.stage', 'features']);
    }

    /**
     * Delete teacher.
     */
    public function deleteTeacher(Teacher $teacher): bool
    {
        if ($teacher->avatar) {
            $this->deleteFile($teacher->avatar);
        }

        return $teacher->delete();
    }
}
