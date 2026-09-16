<?php

namespace Database\Seeders;

use App\Models\EducationalStage;
use App\Models\Grade;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class StageAndGradeSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $stagesData = [
            [
                'name'        => ['ar' => 'مرحلة التمهيدي (رياض الأطفال)', 'en' => 'Kindergarten Stage'],
                'description' => ['ar' => 'مرحلة التأسيس ورياض الأطفال وتشمل مرحلتي KG1 و KG2.', 'en' => 'Early childhood foundation including KG1 and KG2.'],
                'slug'        => 'kindergarten',
                'order'       => 1,
                'grades' => [
                    ['name' => ['ar' => 'المستوى الأول رياض أطفال (KG1)',  'en' => 'KG 1'], 'slug' => 'grade-kg1', 'code' => 'KG-1', 'order' => 1],
                    ['name' => ['ar' => 'المستوى الثاني رياض أطفال (KG2)', 'en' => 'KG 2'], 'slug' => 'grade-kg2', 'code' => 'KG-2', 'order' => 2],
                ],
            ],
            [
                'name'        => ['ar' => 'المرحلة الابتدائية',  'en' => 'Primary Stage'],
                'description' => ['ar' => 'تغطي الصفوف الدراسية من الصف الأول الابتدائي حتى الصف السادس.', 'en' => 'Covers grades from 1st Primary to 6th Primary.'],
                'slug'        => 'primary',
                'order'       => 2,
                'grades' => [
                    ['name' => ['ar' => 'الصف الأول الابتدائي',   'en' => '1st Primary'],   'slug' => 'grade-1',  'code' => 'PRI-1', 'order' => 1],
                    ['name' => ['ar' => 'الصف الثاني الابتدائي',  'en' => '2nd Primary'],   'slug' => 'grade-2',  'code' => 'PRI-2', 'order' => 2],
                    ['name' => ['ar' => 'الصف الثالث الابتدائي', 'en' => '3rd Primary'],    'slug' => 'grade-3',  'code' => 'PRI-3', 'order' => 3],
                    ['name' => ['ar' => 'الصف الرابع الابتدائي',  'en' => '4th Primary'],   'slug' => 'grade-4',  'code' => 'PRI-4', 'order' => 4],
                    ['name' => ['ar' => 'الصف الخامس الابتدائي', 'en' => '5th Primary'],    'slug' => 'grade-5',  'code' => 'PRI-5', 'order' => 5],
                    ['name' => ['ar' => 'الصف السادس الابتدائي', 'en' => '6th Primary'],    'slug' => 'grade-6',  'code' => 'PRI-6', 'order' => 6],
                ],
            ],
            [
                'name'        => ['ar' => 'المرحلة الإعدادية',   'en' => 'Preparatory Stage'],
                'description' => ['ar' => 'تغطي الصفوف الدراسية من الصف الأول الإعدادي حتى الصف الثالث الإعدادي.', 'en' => 'Covers grades from 1st Preparatory to 3rd Preparatory.'],
                'slug'        => 'preparatory',
                'order'       => 3,
                'grades' => [
                    ['name' => ['ar' => 'الصف الأول الإعدادي',   'en' => '1st Preparatory'], 'slug' => 'grade-7',  'code' => 'PREP-1', 'order' => 1],
                    ['name' => ['ar' => 'الصف الثاني الإعدادي',  'en' => '2nd Preparatory'], 'slug' => 'grade-8',  'code' => 'PREP-2', 'order' => 2],
                    ['name' => ['ar' => 'الصف الثالث الإعدادي',  'en' => '3rd Preparatory'], 'slug' => 'grade-9',  'code' => 'PREP-3', 'order' => 3],
                ],
            ],
            [
                'name'        => ['ar' => 'المرحلة الثانوية',    'en' => 'Secondary Stage'],
                'description' => ['ar' => 'تغطي الصفوف الدراسية من الصف الأول الثانوي حتى الصف الثالث الثانوي.', 'en' => 'Covers grades from 1st Secondary to 3rd Secondary.'],
                'slug'        => 'secondary',
                'order'       => 4,
                'grades' => [
                    ['name' => ['ar' => 'الصف الأول الثانوي',    'en' => '1st Secondary'],  'slug' => 'grade-10', 'code' => 'SEC-1', 'order' => 1],
                    ['name' => ['ar' => 'الصف الثاني الثانوي',   'en' => '2nd Secondary'],  'slug' => 'grade-11', 'code' => 'SEC-2', 'order' => 2],
                    ['name' => ['ar' => 'الصف الثالث الثانوي',   'en' => '3rd Secondary'],  'slug' => 'grade-12', 'code' => 'SEC-3', 'order' => 3],
                ],
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($stagesData as $sData) {
                $stage = EducationalStage::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => $sData['slug']],
                    [
                        'name'        => $sData['name'],
                        'description' => $sData['description'],
                        'order'       => $sData['order'],
                        'is_active'   => true,
                    ]
                );

                foreach ($sData['grades'] as $gData) {
                    Grade::updateOrCreate(
                        ['tenant_id' => $tenant->id, 'stage_id' => $stage->id, 'slug' => $gData['slug']],
                        [
                            'name'      => $gData['name'],
                            'code'      => $gData['code'],
                            'order'     => $gData['order'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
