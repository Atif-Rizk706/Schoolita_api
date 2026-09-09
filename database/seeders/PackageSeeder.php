<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $packagesTemplates = [
            [
                'title' => 'الباقة الشهرية - أونلاين',
                'badge' => 'الأكثر طلباً',
                'type' => 'online',
                'price' => 350,
                'currency' => 'ج.م',
                'total_lectures' => 8,
                'weekly_lectures' => 2,
                'hours_per_lecture' => 2.0,
                'is_popular' => true,
                'color_theme' => 'teal',
                'features' => [
                    'بث مباشر تفاعلي مع المعلم',
                    'تسجيلات المحاضرات متاحة طوال الشهر',
                    'بنك أسئلة وامتحانات فورية',
                    'جروب مخصص للأسئلة والدعم',
                ]
            ],
            [
                'title' => 'الباقة الشهرية - سنتر (أوفلاين)',
                'badge' => 'حضور مباشر',
                'type' => 'offline',
                'price' => 500,
                'currency' => 'ج.م',
                'total_lectures' => 8,
                'weekly_lectures' => 2,
                'hours_per_lecture' => 2.5,
                'is_popular' => false,
                'color_theme' => 'amber',
                'features' => [
                    'حضور مباشر في السنتر التعليمي',
                    'مذكرات مطبوعة مجاناً',
                    'متابعة شهرية مع ولي الأمر',
                    'امتحانات ورقية وتقييم دوري',
                ]
            ],
            [
                'title' => 'كارت المحاضرات الفردية - أونلاين',
                'badge' => 'مرونة كاملة',
                'type' => 'online',
                'price' => 60,
                'currency' => 'ج.م',
                'total_lectures' => 1,
                'weekly_lectures' => 1,
                'hours_per_lecture' => 2.0,
                'is_popular' => false,
                'color_theme' => 'blue',
                'features' => [
                    'حجز محاضرة واحدة فقط بحسب اختيارك',
                    'دخول الاختبار التفاعلي الخاص بالمحاضرة',
                    'ملخص PDF ملحق مع المحاضرة',
                ]
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($packagesTemplates as $pData) {
                $features = $pData['features'];
                unset($pData['features']);

                $package = Package::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'title' => $pData['title']],
                    $pData
                );

                $package->features()->delete();
                foreach ($features as $order => $featureText) {
                    PackageFeature::create([
                        'package_id' => $package->id,
                        'feature_text' => $featureText,
                        'order' => $order + 1,
                    ]);
                }
            }
        }
    }
}
