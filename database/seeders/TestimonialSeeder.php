<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $testimonialsData = [
            [
                'name' => 'عمر أحمد',
                'role' => 'طالب بالإعدادية',
                'comment' => 'المنصة خلت الشرح أسهل بكتير! طريقة الدروس التفاعلية بتخليني أذاكر من غير ملل ومستواي اتحسن جداً.',
                'rating' => 5.0,
                'color_theme' => 'amber',
            ],
            [
                'name' => 'م. أمل محمود',
                'role' => 'ولية أمر طالبة',
                'comment' => 'التقارير الدورية بتخليني متابعة مستوى بنتي أول بأول، والمدرسين ممتازين ومتعاونين جداً.',
                'rating' => 5.0,
                'color_theme' => 'teal',
            ],
            [
                'name' => 'يوسف خالد',
                'role' => 'طالب بالثانوية العامة',
                'comment' => 'بنك الأسئلة والتدريبات فرقوا معايا جداً في مراجعة المواد، والتصميم بيشجع على المذاكرة بدون تشتيت.',
                'rating' => 5.0,
                'color_theme' => 'blue',
            ],
            [
                'name' => 'نور ممدوح',
                'role' => 'طالبة بالمرحلة الابتدائية',
                'comment' => 'بحب الفيديوهات التفاعلية جداً والبومة Schoolita بتخليني متحمسة أحل الكويزات وأكسب شارات!',
                'rating' => 5.0,
                'color_theme' => 'purple',
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($testimonialsData as $tData) {
                Testimonial::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'name' => $tData['name']],
                    $tData
                );
            }
        }
    }
}
