<?php

namespace App\Services\Tenant;

use App\Models\EducationalStage;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;

class TenantProfileService
{
    /**
     * Update active tenant settings & branding profile.
     */
    public function updateProfile(Tenant $tenant, array $validated): Tenant
    {
        $tenant->update($validated);
        return $tenant->fresh();
    }

    /**
     * Calculate dashboard statistics and recent summary for tenant.
     */
    public function getDashboardStats(Tenant $tenant): array
    {
        $tenantId = $tenant->id;

        $totalStudents = User::where('tenant_id', $tenantId)->where('role', User::ROLE_STUDENT)->count();
        $totalTeachers = Teacher::where('tenant_id', $tenantId)->count();
        $totalStages = EducationalStage::where('tenant_id', $tenantId)->where('is_active', true)->count();
        $totalSubjects = Subject::where('tenant_id', $tenantId)->count();
        $totalLessons = Lesson::where('tenant_id', $tenantId)->count();
        $totalGroups = SubjectTeacher::whereHas('subject', function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })->count();

        $totalSubscriptions = Subscription::where('tenant_id', $tenantId)->count();
        $activeSubscriptions = Subscription::where('tenant_id', $tenantId)->where('status', 'active')->count();

        // Stages overview with students & teachers count per stage
        $stages = EducationalStage::with(['grades' => function ($q) {
            $q->where('is_active', true)->orderBy('order');
        }])
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($stage) use ($tenantId) {
                $gradeIds = $stage->grades->pluck('id');

                $studentsCount = User::where('tenant_id', $tenantId)
                    ->where('role', User::ROLE_STUDENT)
                    ->whereIn('grade_id', $gradeIds)
                    ->count();

                $teachersCount = Teacher::where('tenant_id', $tenantId)
                    ->whereHas('subjects', function ($q) use ($gradeIds) {
                        $q->whereIn('grade_id', $gradeIds);
                    })
                    ->count();

                return [
                    'id' => $stage->id,
                    'name' => $stage->name,
                    'slug' => $stage->slug,
                    'order' => $stage->order,
                    'grades_count' => $stage->grades->count(),
                    'grades' => $stage->grades->map(fn($g) => [
                        'id' => $g->id,
                        'name' => $g->name,
                        'slug' => $g->slug,
                    ]),
                    'students_count' => $studentsCount,
                    'teachers_count' => $teachersCount,
                ];
            });

        $recentSubscriptions = Subscription::with([
            'user',
            'subjectTeacher.subject',
            'subjectTeacher.teacher'
        ])
            ->where('tenant_id', $tenantId)
            ->latest()
            ->take(5)
            ->get();

        return [
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'logo' => $tenant->logo,
                'cover_image' => $tenant->cover_image,
            ],
            'stats' => [
                'students_count' => $totalStudents,
                'teachers_count' => $totalTeachers,
                'stages_count' => $totalStages,
                'subjects_count' => $totalSubjects,
                'lessons_count' => $totalLessons,
                'groups_count' => $totalGroups,
                'subscriptions_count' => $totalSubscriptions,
                'active_subscriptions_count' => $activeSubscriptions,
            ],
            'stages_overview' => $stages,
            'recent_subscriptions' => $recentSubscriptions,
        ];
    }
}
