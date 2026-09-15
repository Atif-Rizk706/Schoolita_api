<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonAttachment;
use App\Models\SubjectTeacher;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitAndLessonSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = SubjectTeacher::with(['subject', 'teacher'])->get();

        foreach ($assignments as $assignment) {
            $subject = $assignment->subject;
            $teacher = $assignment->teacher;

            if (!$subject || !$teacher) continue;

            // Unit 1
            $unit1 = Unit::updateOrCreate(
                [
                    'tenant_id' => $subject->tenant_id,
                    'subject_teacher_id' => $assignment->id,
                    'title' => "الوحدة الأولى: أساسيات ومفاهيم {$subject->name}",
                ],
                [
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'description' => "مقدمة شاملة لتأسيس الطلاب في الجزء الأول من مادة {$subject->name} مع الأستاذ {$teacher->name}.",
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );

            // Lessons for Unit 1
            $lesson1_1 = Lesson::updateOrCreate(
                [
                    'tenant_id' => $subject->tenant_id,
                    'unit_id' => $unit1->id,
                    'title' => "الدرس الأول: المدخل والمفاهيم الأساسية",
                ],
                [
                    'subject_teacher_id' => $assignment->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'description' => "شرح مفصل وممتع للدرس الأول وتطبيقاته.",
                    'type' => 'bunny_video',
                    'bunny_video_id' => 'v-demo-001',
                    'embed_url' => 'https://iframe.mediadelivery.net/embed/demo/v-demo-001',
                    'duration_minutes' => 45,
                    'is_free_preview' => true,
                    'sort_order' => 1,
                    'is_published' => true,
                ]
            );

            LessonAttachment::updateOrCreate(
                ['lesson_id' => $lesson1_1->id, 'title' => 'مذكرة الدرس الأول (PDF)'],
                ['file_url' => '/downloads/lesson1-summary.pdf', 'file_type' => 'pdf']
            );

            $lesson1_2 = Lesson::updateOrCreate(
                [
                    'tenant_id' => $subject->tenant_id,
                    'unit_id' => $unit1->id,
                    'title' => "الدرس الثاني: التطبيقات العملية وتدريبات المستويات العليا",
                ],
                [
                    'subject_teacher_id' => $assignment->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'description' => "حل أهم 50 سؤالاً متوقعاً في الامتحانات مع أفكار جديدة.",
                    'type' => 'bunny_video',
                    'bunny_video_id' => 'v-demo-002',
                    'embed_url' => 'https://iframe.mediadelivery.net/embed/demo/v-demo-002',
                    'duration_minutes' => 50,
                    'is_free_preview' => false,
                    'sort_order' => 2,
                    'is_published' => true,
                ]
            );

            // Unit 2
            $unit2 = Unit::updateOrCreate(
                [
                    'tenant_id' => $subject->tenant_id,
                    'subject_teacher_id' => $assignment->id,
                    'title' => "الوحدة الثانية: المهارات المتقدمة وبنوك الأسئلة",
                ],
                [
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'description' => "الجزء المتقدم والتطبيقات المكثفة لمادة {$subject->name}.",
                    'sort_order' => 2,
                    'is_active' => true,
                ]
            );

            Lesson::updateOrCreate(
                [
                    'tenant_id' => $subject->tenant_id,
                    'unit_id' => $unit2->id,
                    'title' => "الدرس الأول: مراجعة منتصف الفصل والاختبار الشامل",
                ],
                [
                    'subject_teacher_id' => $assignment->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'description' => "اختبار تقييمي ومراجعة دورية تضمن التميز.",
                    'type' => 'pdf',
                    'pdf_url' => '/downloads/midterm-review.pdf',
                    'duration_minutes' => 30,
                    'is_free_preview' => false,
                    'sort_order' => 1,
                    'is_published' => true,
                ]
            );
        }
    }
}
