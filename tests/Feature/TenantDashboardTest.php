<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\EducationalStage;
use App\Models\Grade;
use App\Models\Package;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $admin;
    protected EducationalStage $stage;
    protected Grade $grade;
    protected Subject $subject;
    protected Teacher $teacher;
    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'مركز النخبة التعليمي',
            'slug' => 'elnokhba',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'د. ياسر المنشاوي',
            'email' => 'admin@elnokhba.com',
            'password' => bcrypt('secret12345'),
            'role' => User::ROLE_TENANT_ADMIN,
        ]);

        $this->stage = EducationalStage::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'المرحلة الثانوية',
            'code' => 'sec',
        ]);

        $this->grade = Grade::create([
            'tenant_id' => $this->tenant->id,
            'stage_id' => $this->stage->id,
            'name' => 'الصف الثالث الثانوي',
            'code' => 'sec-3',
        ]);

        $this->subject = Subject::create([
            'tenant_id' => $this->tenant->id,
            'grade_id' => $this->grade->id,
            'name' => 'الكيمياء',
            'slug' => 'chem-3',
            'is_active' => true,
        ]);

        $this->teacher = Teacher::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'أ. أحمد علي',
            'is_active' => true,
        ]);

        $this->package = Package::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'الباقة الشاملة',
            'type' => 'online',
            'price' => 500,
        ]);
    }

    public function test_tenant_admin_can_login()
    {
        $response = $this->postJson('/api/v1/tenant/login?tenant=elnokhba', [
            'email' => 'admin@elnokhba.com',
            'password' => 'secret12345',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'admin@elnokhba.com');

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_tenant_admin_can_manage_bookings()
    {
        $booking = Booking::create([
            'tenant_id' => $this->tenant->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'package_id' => $this->package->id,
            'student_name' => 'طالب تجريبي',
            'student_phone' => '01012345678',
            'status' => 'pending',
        ]);

        // 1. Fetch Bookings List
        $listResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/tenant/bookings?tenant=elnokhba');

        $listResponse->assertStatus(200)
            ->assertJsonCount(1, 'data.data');

        // 2. Update Status
        $updateResponse = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/tenant/bookings/{$booking->id}/status?tenant=elnokhba", [
                'status' => 'confirmed',
                'notes' => 'تم التأكيد وتفعيل الاشتراك.',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');
    }

    public function test_tenant_admin_can_add_teacher_and_subject()
    {
        // 1. Add Teacher
        $teacherResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/tenant/teachers?tenant=elnokhba', [
                'name' => 'أ. عصام عبد المطلب',
                'title' => 'خبير الفيزياء للثانوية',
                'experience_years' => 15,
            ]);

        $teacherResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $teacherId = $teacherResponse->json('data.id');

        // 2. Add Subject
        $subjectResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/tenant/subjects?tenant=elnokhba', [
                'grade_id' => $this->grade->id,
                'name' => 'الفيزياء الكهربية',
                'teacher_ids' => [$teacherId],
            ]);

        $subjectResponse->assertStatus(201)
            ->assertJsonPath('success', true);
    }
}
