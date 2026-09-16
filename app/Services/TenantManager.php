<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TenantManager
{
    protected ?Tenant $tenant = null;

    /**
     * Set the active tenant for current request.
     */
    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    /**
     * Get the active tenant.
     */
    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    /**
     * Get active tenant ID.
     */
    public function getTenantId(): ?int
    {
        return $this->tenant?->id;
    }

    /**
     * Check if a tenant is currently resolved.
     */
    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Login for Tenant Admin owner.
     */
    public function loginTenantAdmin(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة.'],
            ]);
        }

        if (!$user->isTenantAdmin() && !$user->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'email' => ['عذراً، هذا الحساب غير مصرح له بدخول لوحة تحكم السنتر.'],
            ]);
        }

        $activeTenant = $this->getTenant();
        if ($activeTenant && !$user->isSuperAdmin() && $user->tenant_id !== $activeTenant->id) {
            throw ValidationException::withMessages([
                'tenant' => ['عذراً، هذا الحساب ليس مديراً لـ ' . $activeTenant->name],
            ]);
        }

        $token = $user->createToken('tenant_admin_token')->plainTextToken;

        return [
            'user' => $user->load('tenant'),
            'tenant' => $user->tenant,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Register a new Tenant center and its owner admin.
     */
    public function registerTenant(array $validated): array
    {
        return DB::transaction(function () use ($validated) {
            // 1. Create Tenant
            $tenant = Tenant::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['slug']),
                'domain' => $validated['domain'] ?? null,
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'is_active' => true,
                'subscription_plan' => $validated['subscription_plan'] ?? 'standard',
            ]);

            // Create Tenant Profile
            $tenant->profile()->create([
                'whatsapp' => $validated['whatsapp'] ?? $validated['phone'],
                'address' => $validated['address'] ?? null,
                'primary_color' => $validated['primary_color'] ?? '#2563eb',
                'secondary_color' => $validated['secondary_color'] ?? '#7c3aed',
                'hero_title' => $validated['hero_title'] ?? "مرحباً بكم في {$validated['name']}",
                'hero_subtitle' => $validated['hero_subtitle'] ?? 'منصة التميز والتفوق الدراسي.',
            ]);

            // 2. Create Tenant Admin User
            $admin = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => User::ROLE_TENANT_ADMIN,
                'phone' => $validated['phone'],
            ]);

            // 3. Issue Token
            $token = $admin->createToken('tenant_admin_token')->plainTextToken;

            return [
                'tenant' => $tenant->load('profile'),
                'admin' => $admin,
                'token' => $token,
                'token_type' => 'Bearer',
            ];
        });
    }

    /**
     * Update active tenant settings & branding profile.
     */
    public function updateProfile(Tenant $tenant, array $validated): Tenant
    {
        return app(\App\Services\Tenant\TenantProfileService::class)->updateProfile($tenant, $validated);
    }

    /**
     * Calculate dashboard statistics and recent summary for tenant.
     */
    public function getDashboardStats(Tenant $tenant): array
    {
        $tenantId = $tenant->id;

        $totalStudents = User::where('tenant_id', $tenantId)->where('role', User::ROLE_STUDENT)->count();
        $totalTeachers = Teacher::where('tenant_id', $tenantId)->count();
        $totalSubjects = Subject::where('tenant_id', $tenantId)->count();
        $totalSubscriptions = Subscription::where('tenant_id', $tenantId)->count();
        $activeSubscriptions = Subscription::where('tenant_id', $tenantId)->where('status', 'active')->count();
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
            ],
            'stats' => [
                'students_count' => $totalStudents,
                'teachers_count' => $totalTeachers,
                'subjects_count' => $totalSubjects,
                'subscriptions_count' => $totalSubscriptions,
                'active_subscriptions_count' => $activeSubscriptions,
            ],
            'recent_subscriptions' => $recentSubscriptions,
        ];
    }

    /**
     * Get paginated list of students registered under current tenant.
     */
    public function getStudents(Tenant $tenant, Request $request)
    {
        $query = User::with('grade.stage')
            ->where('tenant_id', $tenant->id)
            ->where('role', User::ROLE_STUDENT);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($request->input('per_page', 15));
    }

    /**
     * Get list of teachers in current tenant.
     */
    public function getTeachers(Tenant $tenant, Request $request)
    {
        $query = Teacher::with(['subjects.grade.stage'])
            ->where('tenant_id', $tenant->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($request->input('per_page', 15));
    }

    /**
     * Store new teacher under current tenant.
     */
    public function storeTeacher(Tenant $tenant, array $data): Teacher
    {
        $data['tenant_id'] = $tenant->id;
        $subjects = $data['subject_ids'] ?? [];
        unset($data['subject_ids']);

        $teacher = Teacher::create($data);

        if (!empty($subjects)) {
            $teacher->subjects()->sync($subjects);
        }

        return $teacher->load('subjects.grade.stage');
    }

    /**
     * Get list of subjects in current tenant.
     */
    public function getSubjects(Tenant $tenant, Request $request)
    {
        $query = Subject::with(['grade.stage', 'teachers'])
            ->where('tenant_id', $tenant->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($request->input('per_page', 15));
    }

    /**
     * Store new subject under current tenant.
     */
    public function storeSubject(Tenant $tenant, array $data): Subject
    {
        $data['tenant_id'] = $tenant->id;
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        }

        $teachers = $data['teacher_ids'] ?? [];
        unset($data['teacher_ids']);

        $subject = Subject::create($data);

        if (!empty($teachers)) {
            $subject->teachers()->sync($teachers);
        }

        return $subject->load(['grade.stage', 'teachers']);
    }

    /**
     * Get contact messages for current tenant.
     */
    public function getContactMessages(Tenant $tenant, Request $request)
    {
        return ContactMessage::where('tenant_id', $tenant->id)
            ->latest()
            ->paginate($request->input('per_page', 15));
    }
}
