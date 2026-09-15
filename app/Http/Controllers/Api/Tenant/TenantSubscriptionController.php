<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreSubscriptionRequest;
use App\Http\Requests\Tenant\UpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Services\Tenant\TenantSubscriptionService;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantSubscriptionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TenantManager $tenantManager,
        protected TenantSubscriptionService $subscriptionService
    ) {
    }

    /**
     * Display listing of subscriptions.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subscriptions = $this->subscriptionService->getSubscriptions($tenant, $request);

        return $this->successResponse(
            SubscriptionResource::collection($subscriptions),
            'تم جلب قائمة الاشتراكات بنجاح / Subscriptions list retrieved successfully'
        );
    }

    /**
     * Store new subscription.
     */
    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subscription = $this->subscriptionService->storeSubscription($tenant, $request->validated());

        return $this->createdResponse(
            new SubscriptionResource($subscription->load(['user', 'subjectTeacher.subject', 'subjectTeacher.teacher'])),
            'تم إضافة الاشتراك بنجاح! / Subscription created successfully!'
        );
    }

    /**
     * Display single subscription details.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subscription = Subscription::with(['user', 'subjectTeacher.subject', 'subjectTeacher.teacher'])
            ->where('tenant_id', $tenant->id)
            ->findOrFail($id);

        return $this->successResponse(new SubscriptionResource($subscription));
    }

    /**
     * Update subscription.
     */
    public function update(UpdateSubscriptionRequest $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subscription = Subscription::where('tenant_id', $tenant->id)->findOrFail($id);

        $updatedSubscription = $this->subscriptionService->updateSubscription($subscription, $request->validated());

        return $this->successResponse(
            new SubscriptionResource($updatedSubscription->load(['user', 'subjectTeacher.subject', 'subjectTeacher.teacher'])),
            'تم تحديث الاشتراك بنجاح / Subscription updated successfully'
        );
    }

    /**
     * Delete subscription.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subscription = Subscription::where('tenant_id', $tenant->id)->findOrFail($id);
        $this->subscriptionService->deleteSubscription($subscription);

        return $this->successResponse(null, 'تم حذف الاشتراك بنجاح / Subscription deleted successfully');
    }
}
