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
                'name' => [
                    'ar' => 'أ. محمد عبد السلام',
                    'en' => 'Mr. Mohamed Abdelsalam',
                ],
                'title' => [
                    'ar' => 'كبير معلمي الرياضيات التطبيقية',
                    'en' => 'Senior Applied Mathematics Teacher',
                ],
                'email' => 'm.abdelsalam@schoolita.com',
                'phone' => '01012345678',
                'address' => 'القاهرة، مصر',
                'avatar' => '/images/image-default.png',
                'bio' => [
                    'ar' => 'خبرة أكثر من 12 عاماً في تدريس الرياضيات للثانوية العامة بأسلوب هندسي تفاعلي مبسط.',
                    'en' => 'Over 12 years of experience teaching Mathematics for high school with an interactive simplified approach.',
                ],
                'features' => [
                    [
                        'title' => [
                            'ar' => 'شرح تفاعلي وبنوك أسئلة شاطرة',
                            'en' => 'Interactive explanations & smart question banks',
                        ],
                        'order' => 0,
                    ],
                    [
                        'title' => [
                            'ar' => 'متابعة دورية واختبارات أسبوعية',
                            'en' => 'Regular follow-up & weekly assessments',
                        ],
                        'order' => 1,
                    ],
                ],
                'experience_years' => 12,
                'students_count' => 3400,
                'rating' => 4.9,
                'whatsapp_number' => '01012345678',
                'color_theme' => 'amber',
            ],
            [
                'name' => [
                    'ar' => 'د. أمنية محمود',
                    'en' => 'Dr. Omnia Mahmoud',
                ],
                'title' => [
                    'ar' => 'مدرسة الكيمياء العضوية وغير العضوية',
                    'en' => 'Organic & Inorganic Chemistry Teacher',
                ],
                'email' => 'omnia.m@schoolita.com',
                'phone' => '01023456789',
                'address' => 'الجيزة، مصر',
                'avatar' => '/images/image-default.png',
                'bio' => [
                    'ar' => 'دكتوراه في العلوم، شرح مبسط لمعادلات الكيمياء وربط النظريات بالتجارب التفاعلية.',
                    'en' => 'Ph.D. in Science, simplified chemistry equations and interactive virtual labs.',
                ],
                'features' => [
                    [
                        'title' => [
                            'ar' => 'تجارب تفاعلية فيرتوال',
                            'en' => 'Interactive virtual experiments',
                        ],
                        'order' => 0,
                    ],
                    [
                        'title' => [
                            'ar' => 'ملخصات شاملة لكل باب',
                            'en' => 'Comprehensive summaries for every unit',
                        ],
                        'order' => 1,
                    ],
                ],
                'experience_years' => 10,
                'students_count' => 2800,
                'rating' => 4.9,
                'whatsapp_number' => '01023456789',
                'color_theme' => 'teal',
            ],
            [
                'name' => [
                    'ar' => 'أ. أحمد حسن',
                    'en' => 'Mr. Ahmed Hassan',
                ],
                'title' => [
                    'ar' => 'مدرس اللغة الإنجليزية',
                    'en' => 'English Language Master Teacher',
                ],
                'email' => 'ahmed.hassan@schoolita.com',
                'phone' => '01034567890',
                'address' => 'الإسكندرية، مصر',
                'avatar' => '/images/image-default.png',
                'bio' => [
                    'ar' => 'خبير مناهج اللغة الإنجليزية للمرحلة الإعدادية واللغات مع بنوك أسئلة وامتحانات فورية.',
                    'en' => 'English curriculum expert for preparatory and language schools with instant quizzes.',
                ],
                'features' => [
                    [
                        'title' => [
                            'ar' => 'تدريب مكثف على مهارات التحدث والكتابة',
                            'en' => 'Intensive training on speaking and writing skills',
                        ],
                        'order' => 0,
                    ],
                ],
                'experience_years' => 8,
                'students_count' => 2200,
                'rating' => 4.8,
                'whatsapp_number' => '01034567890',
                'color_theme' => 'blue',
            ],
            [
                'name' => [
                    'ar' => 'أ. سارة إبراهيم',
                    'en' => 'Ms. Sara Ibrahim',
                ],
                'title' => [
                    'ar' => 'مدرسة الفيزياء الحديثة',
                    'en' => 'Modern Physics Educator',
                ],
                'email' => 'sara.ibrahim@schoolita.com',
                'phone' => '01045678901',
                'address' => 'طنطا، مصر',
                'avatar' => '/images/image-default.png',
                'bio' => [
                    'ar' => 'تفكيك عقد مسائل الفيزياء بأسلوب مشوق وتطبيقات عملية تجعل الاستيعاب مضموناً.',
                    'en' => 'Simplifying complex physics problems with engaging practical applications.',
                ],
                'features' => [
                    [
                        'title' => [
                            'ar' => 'حل جميع أسئلة امتحانات السنوات السابقة',
                            'en' => 'Solving past years exam questions',
                        ],
                        'order' => 0,
                    ],
                ],
                'experience_years' => 9,
                'students_count' => 2600,
                'rating' => 4.9,
                'whatsapp_number' => '01045678901',
                'color_theme' => 'purple',
            ],
            [
                'name' => [
                    'ar' => 'أ. طارق الشريف',
                    'en' => 'Mr. Tarek El-Sherif',
                ],
                'title' => [
                    'ar' => 'مدرس اللغة العربية والنحو',
                    'en' => 'Arabic Language & Grammar Specialist',
                ],
                'email' => 'tarek.elsherif@schoolita.com',
                'phone' => '01056789012',
                'address' => 'المنصورة، مصر',
                'avatar' => '/images/image-default.png',
                'bio' => [
                    'ar' => 'تبسيط قواعد النحو والبلاغة بأسلوب كرتوني وتفاعلي فريد للطلاب الصغار والناشئين.',
                    'en' => 'Simplifying grammar & rhetoric using unique animated visual maps.',
                ],
                'features' => [
                    [
                        'title' => [
                            'ar' => 'خرائط ذهنية لجميع قواعد النحو',
                            'en' => 'Mind maps for all grammar rules',
                        ],
                        'order' => 0,
                    ],
                ],
                'experience_years' => 14,
                'students_count' => 4100,
                'rating' => 4.8,
                'whatsapp_number' => '01056789012',
                'color_theme' => 'amber',
            ],
            [
                'name' => [
                    'ar' => 'د. إسلام يوسف',
                    'en' => 'Dr. Islam Youssef',
                ],
                'title' => [
                    'ar' => 'مدرس الأحياء والجيولوجيا',
                    'en' => 'Biology & Geology Educator',
                ],
                'email' => 'islam.youssef@schoolita.com',
                'phone' => '01067890123',
                'address' => 'أسيوط، مصر',
                'avatar' => '/images/image-default.png',
                'bio' => [
                    'ar' => 'رسومات تفاعلية ثلاثية الأبعاد لشرح تركيب الخلية وجيولوجيا الأرض للثانوية العامة.',
                    'en' => '3D interactive models explaining cell structure & earth geology.',
                ],
                'features' => [
                    [
                        'title' => [
                            'ar' => 'نماذج 3D لشرح أنظمة أجهزة الجسم',
                            'en' => '3D body system breakdown models',
                        ],
                        'order' => 0,
                    ],
                ],
                'experience_years' => 11,
                'students_count' => 3100,
                'rating' => 4.9,
                'whatsapp_number' => '01067890123',
                'color_theme' => 'teal',
            ],
            [
                'name' => [
                    'ar' => 'أ. رانيا فهمي',
                    'en' => 'Ms. Rania Fahmy',
                ],
                'title' => [
                    'ar' => 'مدرسة الدراسات الاجتماعية',
                    'en' => 'Social Studies Teacher',
                ],
                'email' => 'rania.fahmy@schoolita.com',
                'phone' => '01078901234',
                'address' => 'الزقازيق، مصر',
                'avatar' => '/images/image-default.png',
                'bio' => [
                    'ar' => 'رحلات تاريخية وجغرافية ممتعة تجعل مادة الدراسات رحلة استكشاف لا تُنسى.',
                    'en' => 'Fun historical and geographical journeys turning studies into exploration.',
                ],
                'features' => [
                    [
                        'title' => [
                            'ar' => 'خرائط تفاعلية وأسئلة علل ونتائج',
                            'en' => 'Interactive maps & cause and effect questions',
                        ],
                        'order' => 0,
                    ],
                ],
                'experience_years' => 7,
                'students_count' => 1900,
                'rating' => 4.8,
                'whatsapp_number' => '01078901234',
                'color_theme' => 'blue',
            ],
            [
                'name' => [
                    'ar' => 'أ. محمود القاضي',
                    'en' => 'Mr. Mahmoud El-Qady',
                ],
                'title' => [
                    'ar' => 'مدرس الرياضيات والعلوم والاستكشاف',
                    'en' => 'Elementary Science & Math Explorer',
                ],
                'email' => 'mahmoud.elqady@schoolita.com',
                'phone' => '01089012345',
                'address' => 'بنها، مصر',
                'avatar' => '/images/image-default.png',
                'bio' => [
                    'ar' => 'أسلوب كرتوني تفاعلي ومسابقات ذكية تناسب عقول المرحلة الابتدائية بامتياز.',
                    'en' => 'Animated interactive style and smart competitions tailored for elementary minds.',
                ],
                'features' => [
                    [
                        'title' => [
                            'ar' => 'ألعاب تعليمية ومسابقات أسبوعية',
                            'en' => 'Educational games & weekly contests',
                        ],
                        'order' => 0,
                    ],
                ],
                'experience_years' => 6,
                'students_count' => 1750,
                'rating' => 4.9,
                'whatsapp_number' => '01089012345',
                'color_theme' => 'purple',
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($teachersData as $tData) {
                $features = $tData['features'] ?? [];
                unset($tData['features']);

                $teacher = Teacher::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'name->ar' => $tData['name']['ar'],
                    ],
                    $tData
                );

                if (!empty($features)) {
                    $teacher->features()->delete();
                    foreach ($features as $featureData) {
                        $teacher->features()->create($featureData);
                    }
                }
            }
        }
    }
}
