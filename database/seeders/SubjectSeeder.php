<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $subjectsTemplates = [
            [
                'name' => 'الرياضيات التطبيقية',
                'slug' => 'applied-math',
                'grade_slug' => 'grade-12', // 3rd Secondary
                'icon' => 'calculator',
                'color_theme' => 'amber',
                'lessons_count' => 42,
                'subscription_price' => 350.00,
                'teacher_name' => 'أ. محمد عبد السلام',
            ],
            [
                'name' => 'الكيمياء العضوية وغير العضوية',
                'slug' => 'chemistry',
                'grade_slug' => 'grade-12', // 3rd Secondary
                'icon' => 'flask-conical',
                'color_theme' => 'teal',
                'lessons_count' => 38,
                'subscription_price' => 300.00,
                'teacher_name' => 'د. أمنية محمود',
            ],
            [
                'name' => 'اللغة الإنجليزية',
                'slug' => 'english',
                'grade_slug' => 'grade-9', // 3rd Prep
                'icon' => 'languages',
                'color_theme' => 'blue',
                'lessons_count' => 28,
                'subscription_price' => 200.00,
                'teacher_name' => 'أ. أحمد حسن',
            ],
            [
                'name' => 'الفيزياء الحديثة',
                'slug' => 'modern-physics',
                'grade_slug' => 'grade-12', // 3rd Secondary
                'icon' => 'zap',
                'color_theme' => 'purple',
                'lessons_count' => 45,
                'subscription_price' => 350.00,
                'teacher_name' => 'أ. سارة إبراهيم',
            ],
            [
                'name' => 'اللغة العربية والنحو',
                'slug' => 'arabic-grammar',
                'grade_slug' => 'grade-6', // 6th Primary
                'icon' => 'book-open',
                'color_theme' => 'amber',
                'lessons_count' => 30,
                'subscription_price' => 150.00,
                'teacher_name' => 'أ. طارق الشريف',
            ],
            [
                'name' => 'الأحياء والجيولوجيا',
                'slug' => 'biology-geology',
                'grade_slug' => 'grade-12', // 3rd Secondary
                'icon' => 'dna',
                'color_theme' => 'teal',
                'lessons_count' => 35,
                'subscription_price' => 300.00,
                'teacher_name' => 'د. إسلام يوسف',
            ],
            [
                'name' => 'الدراسات الاجتماعية',
                'slug' => 'social-studies',
                'grade_slug' => 'grade-8', // 2nd Prep
                'icon' => 'globe',
                'color_theme' => 'blue',
                'lessons_count' => 24,
                'subscription_price' => 180.00,
                'teacher_name' => 'أ. رانيا فهمي',
            ],
            [
                'name' => 'العلوم والاستكشاف',
                'slug' => 'science-exploration',
                'grade_slug' => 'grade-5', // 5th Primary
                'icon' => 'atom',
                'color_theme' => 'purple',
                'lessons_count' => 20,
                'subscription_price' => 150.00,
                'teacher_name' => 'أ. محمود القاضي',
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($subjectsTemplates as $sData) {
                $grade = Grade::where('tenant_id', $tenant->id)
                    ->where('slug', $sData['grade_slug'])
                    ->first();

                if (!$grade) continue;

                $subject = Subject::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => $sData['slug']],
                    [
                        'grade_id' => $grade->id,
                        'name' => $sData['name'],
                        'description' => "منهج كامل وشامل لمادة {$sData['name']} مع شرح تفاعلي وبنوك أسئلة واختبارات قياس مستوى دورية.",
                        'icon' => $sData['icon'],
                        'color_theme' => $sData['color_theme'],
                        'lessons_count' => $sData['lessons_count'],
                        'subscription_price' => $sData['subscription_price'],
                        'is_active' => true,
                    ]
                );

                // Find teacher in this tenant
                $teacher = Teacher::where('tenant_id', $tenant->id)
                    ->where('name', $sData['teacher_name'])
                    ->first();

                if ($teacher) {
                    $subject->teachers()->syncWithoutDetaching([
                        $teacher->id => ['is_active' => true]
                    ]);
                }
            }
        }
    }
}
