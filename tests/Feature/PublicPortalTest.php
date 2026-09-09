<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPortalTest extends TestCase
{
    public function test_can_fetch_landing_page_data_with_tenant_header(): void
    {
        $response = $this->withHeaders([
            'X-Tenant' => 'schoolita',
            'Accept' => 'application/json',
        ])->getJson('/api/v1/public/landing');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'tenant' => ['id', 'name', 'slug', 'primary_color'],
                'stages',
                'featured_subjects',
                'featured_teachers',
                'packages',
                'testimonials',
            ]
        ]);
    }

    public function test_can_fetch_stages_and_grades_hierarchy(): void
    {
        $response = $this->withHeaders([
            'X-Tenant' => 'schoolita',
        ])->getJson('/api/v1/public/stages-and-grades');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.slug', 'primary');
        $this->assertNotEmpty($response->json('data.0.grades'));
    }

    public function test_can_fetch_subjects_and_filter_by_stage(): void
    {
        $response = $this->withHeaders([
            'X-Tenant' => 'schoolita',
        ])->getJson('/api/v1/public/subjects');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data.data'));
    }

    public function test_can_fetch_booking_wizard_data(): void
    {
        $response = $this->withHeaders([
            'X-Tenant' => 'schoolita',
        ])->getJson('/api/v1/public/booking-data');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'subjects',
                'packages',
            ]
        ]);
    }

    public function test_can_register_and_login_student(): void
    {
        $email = 'new_student_' . uniqid() . '@schoolita.com';

        // Register with full student details
        $regResponse = $this->withHeaders([
            'X-Tenant' => 'schoolita',
        ])->postJson('/api/v1/auth/register', [
            'name' => 'طالب جديد للاختبار',
            'email' => $email,
            'password' => 'secret123',
            'phone' => '01012345678',
            'whatsapp' => '01012345678',
            'parent_email' => 'parent@example.com',
            'parent_phone' => '01098765432',
            'parent_whatsapp' => '01098765432',
            'country' => 'EG',
        ]);

        $regResponse->assertStatus(201);
        $this->assertNotEmpty($regResponse->json('data.token'));
        $this->assertEquals('parent@example.com', $regResponse->json('data.user.parent_email'));

        // Login
        $loginResponse = $this->withHeaders([
            'X-Tenant' => 'schoolita',
        ])->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => 'secret123',
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.token');
        $this->assertNotEmpty($token);

        // Get Me
        $meResponse = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'X-Tenant' => 'schoolita',
        ])->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200);
        $meResponse->assertJsonPath('data.email', $email);
    }
}
