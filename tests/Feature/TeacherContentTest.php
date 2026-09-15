<?php

namespace Tests\Feature;

use App\Models\EducationalStage;
use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherContentTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $teacherUser;
    protected Teacher $teacher;
    protected Subject $subject;
    protected SubjectTeacher $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'مركز الفجر التعليمي',
            'slug' => 'alfajr',
            'is_active' => true,
        ]);

        $stage = EducationalStage::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'المرحلة الإعدادية',
            'code' => 'prep',
        ]);

        $grade = Grade::create([
            'tenant_id' => $this->tenant->id,
            'stage_id' => $stage->id,
            'name' => 'الصف الأول الإعدادي',
            'code' => 'prep-1',
        ]);

        $this->subject = Subject::create([
            'tenant_id' => $this->tenant->id,
            'grade_id' => $grade->id,
            'name' => 'الرياضيات',
            'slug' => 'math-prep-1',
            'is_active' => true,
        ]);

        $this->teacherUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'أستاذ أحمد حسن',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_TEACHER,
        ]);

        $this->teacher = Teacher::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->teacherUser->id,
            'name' => 'أستاذ أحمد حسن',
            'title' => 'معلم أول الرياضيات',
            'is_active' => true,
        ]);

        $this->assignment = SubjectTeacher::create([
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
        ]);
    }

    public function test_teacher_can_fetch_assignments()
    {
        $response = $this->actingAs($this->teacherUser, 'sanctum')
            ->getJson('/api/v1/teacher/my-assignments?tenant=' . $this->tenant->slug);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.assignments');
    }

    public function test_teacher_can_create_unit_and_lesson()
    {
        // 1. Create Unit
        $unitResponse = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/teacher/units?tenant=' . $this->tenant->slug, [
                'subject_teacher_id' => $this->assignment->id,
                'title' => 'الوحدة الأولى: الجبر والأعداد',
                'description' => 'شرح الجبر والعمليات على الأعداد',
                'sort_order' => 1,
            ]);

        $unitResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $unitId = $unitResponse->json('data.id');

        // 2. Create Lesson
        $lessonResponse = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/teacher/lessons?tenant=' . $this->tenant->slug, [
                'unit_id' => $unitId,
                'title' => 'الدرس الأول: الأعداد النسبيّة',
                'description' => 'مقدمة في الأعداد النسبية وتطبيقاتها',
                'type' => 'bunny_video',
                'bunny_video_id' => 'v-12345-abc',
                'duration_minutes' => 35,
                'is_free_preview' => true,
                'sort_order' => 1,
            ]);

        $lessonResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.is_free_preview', true);

        // 3. Fetch Units & Lessons
        $fetchResponse = $this->actingAs($this->teacherUser, 'sanctum')
            ->getJson("/api/v1/teacher/assignments/{$this->assignment->id}/units?tenant=" . $this->tenant->slug);

        $fetchResponse->assertStatus(200)
            ->assertJsonCount(1, 'data.units')
            ->assertJsonCount(1, 'data.units.0.lessons');
    }

    public function test_public_can_view_curriculum()
    {
        $unit = Unit::create([
            'tenant_id' => $this->tenant->id,
            'subject_teacher_id' => $this->assignment->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'الوحدة الأولى: الهندسة',
            'is_active' => true,
        ]);

        Lesson::create([
            'tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'subject_teacher_id' => $this->assignment->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'الدرس الأول: العلاقات بين الزوايا',
            'type' => 'bunny_video',
            'is_published' => true,
        ]);

        $response = $this->getJson("/api/v1/public/curriculum/{$this->assignment->id}?tenant=" . $this->tenant->slug);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.units');
    }
}
