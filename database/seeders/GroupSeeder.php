<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $teachers = Teacher::where('tenant_id', $tenant->id)->get();
            $subjects = Subject::where('tenant_id', $tenant->id)->get();
            $students = User::where('tenant_id', $tenant->id)
                ->where('role', User::ROLE_STUDENT)
                ->get();

            if ($teachers->isEmpty() || $subjects->isEmpty()) {
                continue;
            }

            // Group 1: Primary/Prep Group
            $teacher1 = $teachers->first();
            $subject1 = $subjects->first();

            $group1 = Group::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'code'      => 'GRP-' . strtoupper(substr($tenant->slug, 0, 3)) . '-01',
                ],
                [
                    'teacher_id'  => $teacher1->id,
                    'subject_id'  => $subject1->id,
                    'name'        => 'مجموعة المتفوقين - أ (' . $subject1->name . ')',
                    'capacity'    => 25,
                    'price'       => 350.00,
                    'description' => 'مجموعة دراسية مكثفة تركز على الفهم العميق والتدريب على الامتحانات الدورية.',
                    'is_active'   => true,
                ]
            );

            // Add Days for Group 1 (Sunday & Tuesday)
            $group1->days()->delete();
            $group1->days()->createMany([
                [
                    'day_of_week' => 'الأحد / Sunday',
                    'start_time'  => '16:00',
                    'end_time'    => '18:00',
                    'room'        => 'قاعة العباقرة (A1)',
                ],
                [
                    'day_of_week' => 'الثلاثاء / Tuesday',
                    'start_time'  => '16:00',
                    'end_time'    => '18:00',
                    'room'        => 'قاعة العباقرة (A1)',
                ],
            ]);

            // Group 2: Another Group if second teacher/subject exists
            $teacher2 = $teachers->count() > 1 ? $teachers->get(1) : $teacher1;
            $subject2 = $subjects->count() > 1 ? $subjects->get(1) : $subject1;

            $group2 = Group::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'code'      => 'GRP-' . strtoupper(substr($tenant->slug, 0, 3)) . '-02',
                ],
                [
                    'teacher_id'  => $teacher2->id,
                    'subject_id'  => $subject2->id,
                    'name'        => 'مجموعة التأسيس والتميز - ب (' . $subject2->name . ')',
                    'capacity'    => 20,
                    'price'       => 400.00,
                    'description' => 'مجموعة مخصصة للتأسيس التفاعلي مع حل تدريبات مستمرة وورش عمل.',
                    'is_active'   => true,
                ]
            );

            // Add Days for Group 2 (Monday & Thursday)
            $group2->days()->delete();
            $group2->days()->createMany([
                [
                    'day_of_week' => 'الإثنين / Monday',
                    'start_time'  => '17:30',
                    'end_time'    => '19:30',
                    'room'        => 'قاعة الأبطال (B2)',
                ],
                [
                    'day_of_week' => 'الخميس / Thursday',
                    'start_time'  => '17:30',
                    'end_time'    => '19:30',
                    'room'        => 'قاعة الأبطال (B2)',
                ],
            ]);

            // Enroll sample students
            if ($students->isNotEmpty()) {
                $group1Students = $students->take(3)->pluck('id');
                $group1->students()->syncWithPivotValues($group1Students, [
                    'status'    => 'active',
                    'joined_at' => now(),
                ]);

                if ($students->count() > 2) {
                    $group2Students = $students->slice(1, 3)->pluck('id');
                    $group2->students()->syncWithPivotValues($group2Students, [
                        'status'    => 'active',
                        'joined_at' => now(),
                    ]);
                }
            }
        }
    }
}
