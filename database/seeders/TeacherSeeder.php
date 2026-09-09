<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $teachersData = [
            [
                'name' => 'أ. محمد عبد السلام',
                'title' => 'مدرس الرياضيات التطبيقية',
                'avatar' => '/images/image-default.png',
                'bio' => 'خبرة أكثر من 12 عاماً في تدريس الرياضيات للثانوية العامة بأسلوب هندسي تفاعلي مبسط.',
                'experience_years' => 12,
                'students_count' => 3400,
                'rating' => 4.9,
                'whatsapp_number' => '+201000000000',
                'color_theme' => 'amber',
            ],
            [
                'name' => 'د. أمنية محمود',
                'title' => 'مدرسة الكيمياء العضوية وغير العضوية',
                'avatar' => '/images/image-default.png',
                'bio' => 'دكتوراه في العلوم، شرح مبسط لمعادلات الكيمياء وربط النظريات بالتجارب التفاعلية.',
                'experience_years' => 10,
                'students_count' => 2800,
                'rating' => 4.9,
                'whatsapp_number' => '+201000000000',
                'color_theme' => 'teal',
            ],
            [
                'name' => 'أ. أحمد حسن',
                'title' => 'مدرس اللغة الإنجليزية',
                'avatar' => '/images/image-default.png',
                'bio' => 'خبير مناهج اللغة الإنجليزية للمرحلة الإعدادية واللغات مع بنوك أسئلة وامتحانات فورية.',
                'experience_years' => 8,
                'students_count' => 2200,
                'rating' => 4.8,
                'whatsapp_number' => '+201000000000',
                'color_theme' => 'blue',
            ],
            [
                'name' => 'أ. سارة إبراهيم',
                'title' => 'مدرسة الفيزياء الحديثة',
                'avatar' => '/images/image-default.png',
                'bio' => 'تفكيك عقد مسائل الفيزياء بأسلوب مشوق وتطبيقات عملية تجعل الاستيعاب مضموناً.',
                'experience_years' => 9,
                'students_count' => 2600,
                'rating' => 4.9,
                'whatsapp_number' => '+201000000000',
                'color_theme' => 'purple',
            ],
            [
                'name' => 'أ. طارق الشريف',
                'title' => 'مدرس اللغة العربية والنحو',
                'avatar' => '/images/image-default.png',
                'bio' => 'تبسيط قواعد النحو والبلاغة بأسلوب كرتوني وتفاعلي فريد للطلاب الصغار والناشئين.',
                'experience_years' => 14,
                'students_count' => 4100,
                'rating' => 4.8,
                'whatsapp_number' => '+201000000000',
                'color_theme' => 'amber',
            ],
            [
                'name' => 'د. إسلام يوسف',
                'title' => 'مدرس الأحياء والجيولوجيا',
                'avatar' => '/images/image-default.png',
                'bio' => 'رسومات تفاعلية ثلاثية الأبعاد لشرح تركيب الخلية وجيولوجيا الأرض للثانوية العامة.',
                'experience_years' => 11,
                'students_count' => 3100,
                'rating' => 4.9,
                'whatsapp_number' => '+201000000000',
                'color_theme' => 'teal',
            ],
            [
                'name' => 'أ. رانيا فهمي',
                'title' => 'مدرسة الدراسات الاجتماعية',
                'avatar' => '/images/image-default.png',
                'bio' => 'رحلات تاريخية وجغرافية ممتعة تجعل مادة الدراسات رحلة استكشاف لا تُنسى.',
                'experience_years' => 7,
                'students_count' => 1900,
                'rating' => 4.8,
                'whatsapp_number' => '+201000000000',
                'color_theme' => 'blue',
            ],
            [
                'name' => 'أ. محمود القاضي',
                'title' => 'مدرس الرياضيات والعلوم والاستكشاف',
                'avatar' => '/images/image-default.png',
                'bio' => 'أسلوب كرتوني تفاعلي ومسابقات ذكية تناسب عقول المرحلة الابتدائية بامتياز.',
                'experience_years' => 6,
                'students_count' => 1750,
                'rating' => 4.9,
                'whatsapp_number' => '+201000000000',
                'color_theme' => 'purple',
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($teachersData as $tData) {
                Teacher::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'name' => $tData['name']],
                    $tData
                );
            }
        }
    }
}
