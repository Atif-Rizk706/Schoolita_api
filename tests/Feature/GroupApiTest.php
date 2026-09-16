<?php

namespace Tests\Feature;

use App\Models\EducationalStage;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupApiTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $admin;
    protected Teacher $teacher;
    protected Subject $subject;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->tenant = Tenant::where('slug', 'schoolita')->first();
        $this->admin = User::where('tenant_id', $this->tenant->id)
            ->where('role', User::ROLE_TENANT_ADMIN)
            ->first();
        $this->teacher = Teacher::where('tenant_id', $this->tenant->id)->first();
        $this->subject = Subject::where('tenant_id', $this->tenant->id)->first();
        $this->student = User::where('tenant_id', $this->tenant->id)
            ->where('role', User::ROLE_STUDENT)
            ->first();
    }

    public function test_kindergarten_stage_is_seeded_as_first_stage(): void
    {
        $firstStage = EducationalStage::where('tenant_id', $this->tenant->id)
            ->orderBy('order')
            ->first();

        $this->assertEquals('kindergarten', $firstStage->slug);
        $this->assertEquals(1, $firstStage->order);
        $this->assertCount(2, $firstStage->grades);
    }

    public function test_tenant_admin_can_list_groups_with_days(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/tenant/groups');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'code',
                        'capacity',
                        'price',
                        'teacher' => ['id', 'name'],
                        'subject' => ['id', 'name'],
                        'grade'   => ['id', 'name'],
                        'days'    => [
                            '*' => ['id', 'day_of_week', 'start_time', 'end_time', 'room'],
                        ],
                        'students_count',
                    ],
                ],
            ]);
    }

    public function test_tenant_admin_can_create_group_with_schedule_days(): void
    {
        $payload = [
            'teacher_id'  => $this->teacher->id,
            'subject_id'  => $this->subject->id,
            'name'        => 'مجموعة النخبة الرياضية',
            'capacity'    => 20,
            'price'       => 500,
            'description' => 'مجموعة متقدمة للطلاب المتفوقين',
            'days'        => [
                [
                    'day_of_week' => 'الأحد / Sunday',
                    'start_time'  => '15:00',
                    'end_time'    => '17:00',
                    'room'        => 'قاعة 3',
                ],
                [
                    'day_of_week' => 'الأربعاء / Wednesday',
                    'start_time'  => '15:00',
                    'end_time'    => '17:00',
                    'room'        => 'قاعة 3',
                ],
            ],
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/tenant/groups', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'مجموعة النخبة الرياضية')
            ->assertJsonCount(2, 'data.days');

        $groupId = $response->json('data.id');
        $this->assertDatabaseHas('groups', ['id' => $groupId, 'name' => 'مجموعة النخبة الرياضية']);
        $this->assertDatabaseHas('group_days', ['group_id' => $groupId, 'day_of_week' => 'الأحد / Sunday']);
    }

    public function test_tenant_admin_can_add_and_remove_student_from_group(): void
    {
        $group = Group::where('tenant_id', $this->tenant->id)->first();

        // 1. Add Student
        $addResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/tenant/groups/{$group->id}/students", [
                'user_id' => $this->student->id,
                'status'  => 'active',
                'notes'   => 'طالب متميز',
            ]);

        $addResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('group_users', [
            'group_id' => $group->id,
            'user_id'  => $this->student->id,
            'status'   => 'active',
        ]);

        // 2. Remove Student
        $removeResponse = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/tenant/groups/{$group->id}/students/{$this->student->id}");

        $removeResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('group_users', [
            'group_id' => $group->id,
            'user_id'  => $this->student->id,
        ]);
    }

    public function test_dashboard_stats_includes_actual_groups_count(): void
    {
        $groupsCountInDb = Group::where('tenant_id', $this->tenant->id)->count();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/tenant/dashboard-stats');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.stats.groups_count', $groupsCountInDb);
    }
}
