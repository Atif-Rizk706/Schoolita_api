<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_new_tenant_and_admin()
    {
        $payload = [
            'name' => 'أكاديمية المستقبل التعليمية',
            'slug' => 'future-academy',
            'domain' => 'future-academy.com',
            'phone' => '01011223344',
            'whatsapp' => '01011223344',
            'email' => 'contact@future-academy.com',
            'primary_color' => '#10b981',
            'admin_name' => 'د. حسام الدين',
            'admin_email' => 'hossam@future-academy.com',
            'admin_password' => 'secret12345',
        ];

        $response = $this->postJson('/api/v1/tenant/register', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.tenant.slug', 'future-academy')
            ->assertJsonPath('data.admin.email', 'hossam@future-academy.com');

        $this->assertDatabaseHas('tenants', ['slug' => 'future-academy']);
        $this->assertDatabaseHas('users', ['email' => 'hossam@future-academy.com', 'role' => User::ROLE_TENANT_ADMIN]);
    }

    public function test_tenant_admin_can_view_and_update_profile()
    {
        $tenant = Tenant::create([
            'name' => 'أكاديمية الإبداع',
            'slug' => 'ebda3',
            'is_active' => true,
        ]);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'مدير أكاديمية الإبداع',
            'email' => 'admin@ebda3.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_TENANT_ADMIN,
        ]);

        // Get Profile
        $profileResponse = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/tenant/profile?tenant=ebda3');

        $profileResponse->assertStatus(200)
            ->assertJsonPath('data.slug', 'ebda3');

        // Update Profile
        $updateResponse = $this->actingAs($admin, 'sanctum')
            ->putJson('/api/v1/tenant/profile?tenant=ebda3', [
                'primary_color' => '#f43f5e',
                'hero_title' => 'أكاديمية الإبداع - أهلاً بكم',
                'phone' => '01234567890',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.profile.primary_color', '#f43f5e');

        $this->assertDatabaseHas('tenants', ['slug' => 'ebda3', 'phone' => '01234567890']);
        $this->assertDatabaseHas('tenant_profiles', ['primary_color' => '#f43f5e']);
    }

    public function test_tenant_admin_can_fetch_dashboard_stats()
    {
        $tenant = Tenant::create([
            'name' => 'مركز الأوائل',
            'slug' => 'alawaael',
            'is_active' => true,
        ]);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'مدير مركز الأوائل',
            'email' => 'admin@alawaael.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_TENANT_ADMIN,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/tenant/dashboard-stats?tenant=alawaael');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'tenant' => ['id', 'name', 'slug'],
                    'stats' => ['students_count', 'teachers_count', 'subjects_count', 'subscriptions_count'],
                ]
            ]);
    }
}
