<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TenantAuthService
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    /**
     * Login for Tenant Admin owner.
     */
    public function login(string $email, string $password): array
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

        $activeTenant = $this->tenantManager->getTenant();
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
    public function register(array $validated): array
    {
        return DB::transaction(function () use ($validated) {
            $tenant = Tenant::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['slug']),
                'domain' => $validated['domain'] ?? null,
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'is_active' => true,
                'subscription_plan' => $validated['subscription_plan'] ?? 'standard',
            ]);

            $tenant->profile()->create([
                'whatsapp' => $validated['whatsapp'] ?? $validated['phone'],
                'address' => $validated['address'] ?? null,
                'primary_color' => $validated['primary_color'] ?? '#2563eb',
                'secondary_color' => $validated['secondary_color'] ?? '#7c3aed',
                'hero_title' => $validated['hero_title'] ?? "مرحباً بكم في {$validated['name']}",
                'hero_subtitle' => $validated['hero_subtitle'] ?? 'منصة التميز والتفوق الدراسي.',
            ]);

            $admin = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => User::ROLE_TENANT_ADMIN,
                'phone' => $validated['phone'],
            ]);

            $token = $admin->createToken('tenant_admin_token')->plainTextToken;

            return [
                'tenant' => $tenant,
                'admin' => $admin,
                'token' => $token,
                'token_type' => 'Bearer',
            ];
        });
    }
}
