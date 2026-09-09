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
                'name' => 'المرحلة الابتدائية',
                'slug' => 'primary',
                'order' => 1,
                'grades' => [
                    ['name' => 'الصف الأول الابتدائي', 'slug' => 'grade-1', 'order' => 1],
                    ['name' => 'الصف الثاني الابتدائي', 'slug' => 'grade-2', 'order' => 2],
                    ['name' => 'الصف الثالث الابتدائي', 'slug' => 'grade-3', 'order' => 3],
                    ['name' => 'الصف الرابع الابتدائي', 'slug' => 'grade-4', 'order' => 4],
                    ['name' => 'الصف الخامس الابتدائي', 'slug' => 'grade-5', 'order' => 5],
                    ['name' => 'الصف السادس الابتدائي', 'slug' => 'grade-6', 'order' => 6],
                ]
            ],
            [
                'name' => 'المرحلة الإعدادية',
                'slug' => 'preparatory',
                'order' => 2,
                'grades' => [
                    ['name' => 'الصف الأول الإعدادي', 'slug' => 'grade-7', 'order' => 1],
                    ['name' => 'الصف الثاني الإعدادي', 'slug' => 'grade-8', 'order' => 2],
                    ['name' => 'الصف الثالث الإعدادي', 'slug' => 'grade-9', 'order' => 3],
                ]
            ],
            [
                'name' => 'المرحلة الثانوية',
                'slug' => 'secondary',
                'order' => 3,
                'grades' => [
                    ['name' => 'الصف الأول الثانوي', 'slug' => 'grade-10', 'order' => 1],
                    ['name' => 'الصف الثاني الثانوي', 'slug' => 'grade-11', 'order' => 2],
                    ['name' => 'الصف الثالث الثانوي', 'slug' => 'grade-12', 'order' => 3],
                ]
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($stagesData as $sData) {
                $stage = EducationalStage::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => $sData['slug']],
                    [
                        'name' => $sData['name'],
                        'order' => $sData['order'],
                        'is_active' => true,
                    ]
                );

                foreach ($sData['grades'] as $gData) {
                    Grade::updateOrCreate(
                        ['tenant_id' => $tenant->id, 'stage_id' => $stage->id, 'slug' => $gData['slug']],
                        [
                            'name' => $gData['name'],
                            'order' => $gData['order'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
