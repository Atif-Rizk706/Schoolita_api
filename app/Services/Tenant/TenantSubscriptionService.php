<?php

namespace App\Services\Tenant;

use App\Models\SubjectTeacher;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantSubscriptionService
{
    /**
     * Get subscriptions list for current tenant with filters.
     */
    public function getSubscriptions(Tenant $tenant, Request $request)
    {
        $query = Subscription::with([
            'user',
            'subjectTeacher.subject.grade.stage',
            'subjectTeacher.teacher'
        ])->where('tenant_id', $tenant->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('subject_teacher_id')) {
            $query->where('subject_teacher_id', $request->subject_teacher_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->paginate($request->input('per_page', 15));
    }

    /**
     * Store subscription.
     */
    public function storeSubscription(Tenant $tenant, array $data): Subscription
    {
        $data['tenant_id'] = $tenant->id;

        // If price is not provided, fetch subject's subscription_price
        if (!isset($data['price']) || $data['price'] === null) {
            $subjectTeacher = SubjectTeacher::with('subject')->findOrFail($data['subject_teacher_id']);
            $data['price'] = $subjectTeacher->subject->subscription_price ?? 0.00;
        }

        $subscription = Subscription::create($data);

        return $subscription->load([
            'user',
            'subjectTeacher.subject.grade.stage',
            'subjectTeacher.teacher'
        ]);
    }

    /**
     * Update subscription.
     */
    public function updateSubscription(Subscription $subscription, array $data): Subscription
    {
        $subscription->update($data);

        return $subscription->load([
            'user',
            'subjectTeacher.subject.grade.stage',
            'subjectTeacher.teacher'
        ]);
    }

    /**
     * Delete subscription.
     */
    public function deleteSubscription(Subscription $subscription): bool
    {
        return $subscription->delete();
    }
}
