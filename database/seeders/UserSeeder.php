<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $schoolita = Tenant::where('slug', 'schoolita')->first();
        $elnour = Tenant::where('slug', 'elnour')->first();

        // 1. Super Admin (System Owner - Not tied to single tenant)
        User::updateOrCreate(
            ['email' => 'superadmin@scolita.com'],
            [
                'name' => 'مدير عام المنصة (Super Admin)',
                'phone' => '01000000000',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_SUPER_ADMIN,
            ]
        );

        // 2. Schoolita Users
        if ($schoolita) {
            User::updateOrCreate(
                ['email' => 'admin@schoolita.com'],
                [
                    'tenant_id' => $schoolita->id,
                    'name' => 'إدارة منصة سكوليتا',
                    'phone' => '01000000001',
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_TENANT_ADMIN,
                ]
            );

            $grade9 = Grade::where('tenant_id', $schoolita->id)->where('slug', 'grade-9')->first();
            User::updateOrCreate(
                ['email' => 'student@schoolita.com'],
                [
                    'tenant_id' => $schoolita->id,
                    'name' => 'عمر أحمد محمد',
                    'phone' => '01000000002',
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_STUDENT,
                    'grade_id' => $grade9?->id,
                ]
            );
        }

        // 3. El-Nour Academy Users
        if ($elnour) {
            User::updateOrCreate(
                ['email' => 'admin@elnour.com'],
                [
                    'tenant_id' => $elnour->id,
                    'name' => 'إدارة سنتر النور',
                    'phone' => '01112223331',
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_TENANT_ADMIN,
                ]
            );

            $grade12 = Grade::where('tenant_id', $elnour->id)->where('slug', 'grade-12')->first();
            User::updateOrCreate(
                ['email' => 'student@elnour.com'],
                [
                    'tenant_id' => $elnour->id,
                    'name' => 'مريم خالد إبراهيم',
                    'phone' => '01112223332',
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_STUDENT,
                    'grade_id' => $grade12?->id,
                ]
            );
        }
    }
}
