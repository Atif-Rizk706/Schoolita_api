<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Tenant 1: Schoolita
        Tenant::updateOrCreate(
            ['slug' => 'schoolita'],
            [
                'name' => 'سكوليتا | منصة التعليم الذكي للأبطال',
                'domain' => 'schoolita.com',
                'logo' => '/logo.png',
                'cover_image' => '/images/og-image.png',
                'primary_color' => '#f59e0b',
                'secondary_color' => '#0d9488',
                'phone' => '+20 100 000 0000',
                'whatsapp' => '201000000000',
                'email' => 'support@schoolita.com',
                'address' => 'القاهرة، جمهورية مصر العربية',
                'working_hours' => 'من الأحد للخميس: 9 صباحاً - 6 مساءً',
                'social_links' => [
                    'facebook' => 'https://facebook.com',
                    'youtube' => 'https://youtube.com',
                    'instagram' => 'https://instagram.com',
                    'whatsapp' => 'https://wa.me/201000000000',
                    'tiktok' => 'https://tiktok.com',
                ],
                'hero_title' => 'تعلم بذكاء ومتعة مع Schoolita',
                'hero_subtitle' => 'تجربة تعليمية تفاعلية جديدة تدمج المتعة بالتفوق لكل المراحل الدراسية مع نخبة من أفضل المعلمين.',
                'stats' => [
                    'students_count' => '+15,000',
                    'subjects_count' => '+12',
                    'teachers_count' => '+50',
                    'satisfaction_rate' => '99%',
                ],
                'is_active' => true,
                'subscription_plan' => 'pro',
            ]
        );

        // Tenant 2: Al-Nour Academy (To demonstrate multi-tenancy)
        Tenant::updateOrCreate(
            ['slug' => 'elnour'],
            [
                'name' => 'أكاديمية النور التعليمية',
                'domain' => 'elnour-academy.com',
                'logo' => '/logo.png',
                'cover_image' => '/images/og-image.png',
                'primary_color' => '#2563eb',
                'secondary_color' => '#7c3aed',
                'phone' => '+20 111 222 3333',
                'whatsapp' => '201112223333',
                'email' => 'info@elnour-academy.com',
                'address' => 'الجيزة، مصر',
                'working_hours' => 'يومياً: 8 صباحاً - 10 مساءً',
                'social_links' => [
                    'facebook' => 'https://facebook.com',
                    'youtube' => 'https://youtube.com',
                    'whatsapp' => 'https://wa.me/201112223333',
                ],
                'hero_title' => 'مستقبلك يبدأ من هنا مع أكاديمية النور',
                'hero_subtitle' => 'نقدم أقوى المناهج وشروحات الثانوية العامة والإعدادية مع كبار الأساتذة والمتابعة المستمرة.',
                'stats' => [
                    'students_count' => '+8,500',
                    'subjects_count' => '+10',
                    'teachers_count' => '+25',
                    'satisfaction_rate' => '98%',
                ],
                'is_active' => true,
                'subscription_plan' => 'standard',
            ]
        );
    }
}
