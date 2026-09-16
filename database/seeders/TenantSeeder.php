<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Tenant 1: Schoolita
        $tenant1 = Tenant::updateOrCreate(
            ['slug' => 'schoolita'],
            [
                'name' => 'سكوليتا | منصة التعليم الذكي للأبطال',
                'domain' => 'schoolita.com',
                'phone' => '+20 100 000 0000',
                'email' => 'support@schoolita.com',
                'is_active' => true,
                'subscription_plan' => 'pro',
            ]
        );

        $tenant1->profile()->updateOrCreate(
            ['tenant_id' => $tenant1->id],
            [
                'logo' => '/logo.png',
                'cover_image' => '/images/og-image.png',
                'primary_color' => '#f59e0b',
                'secondary_color' => '#0d9488',
                'whatsapp' => '201000000000',
                'address' => [
                    'ar' => 'القاهرة، جمهورية مصر العربية',
                    'en' => 'Cairo, Egypt',
                ],
                'working_hours' => [
                    'ar' => 'من الأحد للخميس: 9 صباحاً - 6 مساءً',
                    'en' => 'Sunday to Thursday: 9 AM - 6 PM',
                ],
                'about_us' => [
                    'ar' => 'منصة سكوليتا هي منصة تعليمية رائدة تقدم أفضل تجربة تعليمية تفاعلية للطلاب والمعلمين.',
                    'en' => 'Schoolita is a leading educational platform delivering interactive learning experiences.',
                ],
                'vision' => [
                    'ar' => 'تمكين كل طالب ومعلم من أدوات التعلم الذكي الحديثة.',
                    'en' => 'Empowering every student and teacher with modern smart learning tools.',
                ],
                'mission' => [
                    'ar' => 'تقديم تعليم تفاعلي عالي الجودة وبأحدث التقنيات وبأسعار مناسبة للجميع.',
                    'en' => 'Delivering high quality interactive education with modern technology accessible to all.',
                ],
                'hero_title' => [
                    'ar' => 'تعلم بذكاء ومتعة مع Schoolita',
                    'en' => 'Learn Smart & Enjoyably with Schoolita',
                ],
                'hero_subtitle' => [
                    'ar' => 'تجربة تعليمية تفاعلية جديدة تدمج المتعة بالتفوق لكل المراحل الدراسية مع نخبة من أفضل المعلمين.',
                    'en' => 'A new interactive educational experience blending fun with excellence across all grades.',
                ],
                'social_links' => [
                    'facebook' => 'https://facebook.com',
                    'youtube' => 'https://youtube.com',
                    'instagram' => 'https://instagram.com',
                    'whatsapp' => 'https://wa.me/201000000000',
                    'tiktok' => 'https://tiktok.com',
                ],
            ]
        );

        // Tenant 2: Al-Nour Academy (To demonstrate multi-tenancy)
        $tenant2 = Tenant::updateOrCreate(
            ['slug' => 'elnour'],
            [
                'name' => 'أكاديمية النور التعليمية',
                'domain' => 'elnour-academy.com',
                'phone' => '+20 111 222 3333',
                'email' => 'info@elnour-academy.com',
                'is_active' => true,
                'subscription_plan' => 'standard',
            ]
        );

        $tenant2->profile()->updateOrCreate(
            ['tenant_id' => $tenant2->id],
            [
                'logo' => '/logo.png',
                'cover_image' => '/images/og-image.png',
                'primary_color' => '#2563eb',
                'secondary_color' => '#7c3aed',
                'whatsapp' => '201112223333',
                'address' => [
                    'ar' => 'الجيزة، مصر',
                    'en' => 'Giza, Egypt',
                ],
                'working_hours' => [
                    'ar' => 'يومياً: 8 صباحاً - 10 مساءً',
                    'en' => 'Daily: 8 AM - 10 PM',
                ],
                'about_us' => [
                    'ar' => 'أكاديمية النور التعليمية لتقديم شروحات المناهج لجميع المراحل الدراسية.',
                    'en' => 'Al-Nour Educational Academy providing curriculum lessons for all stages.',
                ],
                'vision' => [
                    'ar' => 'الريادة في التعليم والمتابعة الأكاديمية المستمرة.',
                    'en' => 'Leadership in education and continuous academic follow-up.',
                ],
                'mission' => [
                    'ar' => 'مساعدة الطلاب على تحقيق أعلى الدرجات العلمية.',
                    'en' => 'Helping students achieve the highest academic scores.',
                ],
                'hero_title' => [
                    'ar' => 'مستقبلك يبدأ من هنا مع أكاديمية النور',
                    'en' => 'Your future starts here with Al-Nour Academy',
                ],
                'hero_subtitle' => [
                    'ar' => 'نقدم أقوى المناهج وشروحات الثانوية العامة والإعدادية مع كبار الأساتذة والمتابعة المستمرة.',
                    'en' => 'We provide the strongest curricula with top teachers and continuous tracking.',
                ],
                'social_links' => [
                    'facebook' => 'https://facebook.com',
                    'youtube' => 'https://youtube.com',
                    'whatsapp' => 'https://wa.me/201112223333',
                ],
            ]
        );
    }
}
