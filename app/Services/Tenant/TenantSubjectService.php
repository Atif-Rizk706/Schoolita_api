<?php

namespace App\Services\Tenant;

use App\Models\Subject;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantSubjectService
{
    /**
     * Get list of subjects for current tenant.
     */
    public function getSubjects(Tenant $tenant, Request $request)
    {
        $query = Subject::with(['grade.stage', 'teachers', 'outcomes', 'features'])
            ->where('tenant_id', $tenant->id);

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($request->input('per_page', 15));
    }

    /**
     * Store new subject with outcomes and features.
     */
    public function storeSubject(Tenant $tenant, array $data): Subject
    {
        $data['tenant_id'] = $tenant->id;

        if (empty($data['slug'])) {
            $nameStr = is_array($data['name'])
                ? ($data['name']['ar'] ?? $data['name']['en'] ?? reset($data['name']))
                : $data['name'];
            $data['slug'] = Str::slug($nameStr) . '-' . Str::random(4);
        }

        // Extract relations before creating subject
        $teachers = $data['teacher_ids'] ?? [];
        $outcomes  = $data['outcomes'] ?? [];
        $features  = $data['features'] ?? [];
        unset($data['teacher_ids'], $data['outcomes'], $data['features']);

        $subject = Subject::create($data);

        // Sync teachers
        if (!empty($teachers)) {
            $subject->teachers()->sync($teachers);
        }

        // Create outcomes (مخرجات التعلم)
        foreach ($outcomes as $index => $item) {
            $subject->outcomes()->create([
                'title' => $item['title'],
                'order' => $item['order'] ?? $index,
            ]);
        }

        // Create features (المميزات)
        foreach ($features as $index => $item) {
            $subject->features()->create([
                'title' => $item['title'],
                'order' => $item['order'] ?? $index,
            ]);
        }

        return $subject->load(['grade.stage', 'teachers', 'outcomes', 'features']);
    }

    /**
     * Update subject with outcomes and features (full replace strategy).
     */
    public function updateSubject(Subject $subject, array $data): Subject
    {
        // Sync teachers
        if (isset($data['teacher_ids'])) {
            $subject->teachers()->sync($data['teacher_ids']);
            unset($data['teacher_ids']);
        }

        // Replace outcomes if provided
        if (array_key_exists('outcomes', $data)) {
            $subject->outcomes()->delete();
            foreach (($data['outcomes'] ?? []) as $index => $item) {
                $subject->outcomes()->create([
                    'title' => $item['title'],
                    'order' => $item['order'] ?? $index,
                ]);
            }
            unset($data['outcomes']);
        }

        // Replace features if provided
        if (array_key_exists('features', $data)) {
            $subject->features()->delete();
            foreach (($data['features'] ?? []) as $index => $item) {
                $subject->features()->create([
                    'title' => $item['title'],
                    'order' => $item['order'] ?? $index,
                ]);
            }
            unset($data['features']);
        }

        $subject->update($data);

        return $subject->load(['grade.stage', 'teachers', 'outcomes', 'features']);
    }

    /**
     * Toggle subject active status (is_active).
     */
    public function toggleStatus(Subject $subject): Subject
    {
        $subject->update(['is_active' => !$subject->is_active]);

        return $subject->fresh(['grade.stage', 'teachers', 'outcomes', 'features']);
    }

    /**
     * Delete subject (cascades to outcomes and features via FK).
     */
    public function deleteSubject(Subject $subject): bool
    {
        return $subject->delete();
    }
}
