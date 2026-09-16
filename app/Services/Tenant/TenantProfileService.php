<?php

namespace App\Services\Tenant;

use App\Models\EducationalStage;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\FileUploadTrait;

class TenantProfileService
{
    use FileUploadTrait;

    /**
     * Update active tenant settings & branding profile.
     */
    public function updateProfile(Tenant $tenant, array $validated): Tenant
    {
        // 1. Update Core Tenant fields
        $tenantFields = array_intersect_key($validated, array_flip([
            'name', 'phone', 'email'
        ]));

        if (!empty($tenantFields)) {
            $tenant->update($tenantFields);
        }

        // 2. Update or Create Tenant Profile
        $profile = $tenant->profile ?? $tenant->profile()->create([]);

        $profileFields = array_intersect_key($validated, array_flip([
            'primary_color', 'secondary_color', 'whatsapp', 'address',
            'working_hours', 'about_us', 'vision', 'mission',
            'hero_title', 'hero_subtitle', 'social_links'
        ]));

        if (isset($validated['logo'])) {
            $profileFields['logo'] = $this->uploadFile($validated['logo'], 'tenants', $profile->logo);
        }

        if (isset($validated['cover_image'])) {
            $profileFields['cover_image'] = $this->uploadFile($validated['cover_image'], 'tenants', $profile->cover_image);
        }

        if (!empty($profileFields)) {
            $profile->update($profileFields);
        }

        return $tenant->fresh(['profile']);
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
        $totalGroups = Group::where('tenant_id', $tenantId)->count();

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

                $locale = request()->header('Accept-Language', app()->getLocale());
                $locale = in_array(substr($locale, 0, 2), ['ar', 'en']) ? substr($locale, 0, 2) : app()->getLocale();

                return [
                    'id' => $stage->id,
                    'name' => $stage->getTranslation('name', $locale, true) ?: $stage->name,
                    'slug' => $stage->slug,
                    'order' => $stage->order,
                    'grades_count' => $stage->grades->count(),
                    'grades' => $stage->grades->map(fn($g) => [
                        'id' => $g->id,
                        'name' => $g->getTranslation('name', $locale, true) ?: $g->name,
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
