<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            StageAndGradeSeeder::class,
            UserSeeder::class,
            TeacherSeeder::class,
            SubjectSeeder::class,
            TestimonialSeeder::class,
            UnitAndLessonSeeder::class,
            SubscriptionSeeder::class,
        ]);
    }
}
