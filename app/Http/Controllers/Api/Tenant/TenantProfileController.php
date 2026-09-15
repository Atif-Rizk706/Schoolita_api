<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenantResource;
use App\Services\Tenant\TenantProfileService;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantProfileController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TenantManager $tenantManager,
        protected TenantProfileService $profileService
    ) {
    }

    /**
     * Get active tenant settings & profile details.
     */
    public function show(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        if (!$tenant) {
            return $this->notFoundResponse('لم يتم العثور على بيانات السنتر / Tenant not found');
        }

        return $this->successResponse(new TenantResource($tenant));
    }

    /**
     * Update active tenant settings & branding.
     */
    public function update(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        if (!$tenant) {
            return $this->notFoundResponse('لم يتم العثور على بيانات السنتر / Tenant not found');
        }

        $validated = $request->validate([
            'name'            => 'sometimes|required|string|max:255',
            'logo'            => 'nullable|image|max:2048',
            'cover_image'     => 'nullable|image|max:4096',
            'primary_color'   => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'phone'           => 'nullable|string|max:30',
            'whatsapp'        => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:150',
            'address'         => 'nullable|string|max:255',
            'about_us'        => 'nullable|string',
            'vision'          => 'nullable|string',
            'mission'         => 'nullable|string',
            'working_hours'   => 'nullable|string|max:255',
            'social_links'    => 'nullable|array',
            'hero_title'      => 'nullable|string|max:255',
            'hero_subtitle'   => 'nullable|string|max:500',
            'stats'           => 'nullable|array',
        ]);

        $updatedTenant = $this->profileService->updateProfile($tenant, $validated);

        return $this->successResponse(
            new TenantResource($updatedTenant),
            'تم تحديث هوية وإعدادات السنتر بنجاح / Tenant profile updated successfully'
        );
    }

    /**
     * Get dashboard summary & quick stats for tenant admin.
     */
    public function dashboardStats(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        if (!$tenant) {
            return $this->notFoundResponse('لم يتم العثور على بيانات السنتر / Tenant not found');
        }

        $statsData = $this->profileService->getDashboardStats($tenant);

        return $this->successResponse($statsData);
    }
}
