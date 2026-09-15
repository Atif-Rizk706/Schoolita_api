<?php

namespace Database\Seeders;

use App\Models\SubjectTeacher;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $students = User::where('tenant_id', $tenant->id)
                ->where('role', User::ROLE_STUDENT)
                ->take(5)
                ->get();

            $subjectTeachers = SubjectTeacher::with('subject')
                ->whereHas('subject', function ($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id);
                })
                ->get();

            if ($students->isEmpty() || $subjectTeachers->isEmpty()) {
                continue;
            }

            foreach ($students as $index => $student) {
                $st = $subjectTeachers->random();

                Subscription::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'user_id' => $student->id,
                        'subject_teacher_id' => $st->id,
                    ],
                    [
                        'price' => $st->subject->subscription_price ?? 200.00,
                        'currency' => 'EGP',
                        'payment_method' => $index % 2 === 0 ? 'cash' : 'vodafone_cash',
                        'status' => 'active',
                        'starts_at' => now()->startOfMonth(),
                        'ends_at' => now()->addMonths(3)->endOfMonth(),
                        'notes' => 'اشتراك تجريبي عبر البذر (Seeder).',
                    ]
                );
            }
        }
    }
}
