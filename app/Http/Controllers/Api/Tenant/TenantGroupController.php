<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Services\Tenant\TenantGroupService;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantGroupController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TenantManager $tenantManager,
        protected TenantGroupService $groupService
    ) {
    }

    /**
     * Display a listing of the groups.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $groups = $this->groupService->getGroups($tenant, $request);

        return $this->successResponse(
            GroupResource::collection($groups),
            'تم جلب قائمة المجموعات بنجاح / Groups retrieved successfully'
        );
    }

    /**
     * Store a newly created group in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        $validated = $request->validate([
            'teacher_id'        => 'required|exists:teachers,id',
            'subject_id'        => 'required|exists:subjects,id',
            'name'              => 'required|string|max:150',
            'capacity'          => 'nullable|integer|min:1',
            'price'             => 'nullable|numeric|min:0',
            'description'       => 'nullable|string|max:1000',
            'is_active'         => 'nullable|boolean',
            'days'              => 'nullable|array',
            'days.*.day_of_week'=> 'required_with:days|string|max:30',
            'days.*.start_time' => 'required_with:days|string',
            'days.*.end_time'   => 'required_with:days|string',
            'days.*.room'       => 'nullable|string|max:100',
            'student_ids'       => 'nullable|array',
            'student_ids.*'     => 'exists:users,id',
        ]);

        $group = $this->groupService->storeGroup($tenant, $validated);

        return $this->createdResponse(
            new GroupResource($group),
            'تم إنشاء المجموعة الدراسية بنجاح / Group created successfully'
        );
    }

    /**
     * Display the specified group.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $group = Group::with(['teacher', 'subject.grade.stage', 'days', 'students'])
            ->where('tenant_id', $tenant->id)
            ->findOrFail($id);

        return $this->successResponse(
            new GroupResource($group),
            'تم جلب تفاصيل المجموعة بنجاح / Group details retrieved successfully'
        );
    }

    /**
     * Update the specified group in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $group = Group::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'teacher_id'        => 'sometimes|required|exists:teachers,id',
            'subject_id'        => 'sometimes|required|exists:subjects,id',
            'name'              => 'sometimes|required|string|max:150',
            'capacity'          => 'nullable|integer|min:1',
            'price'             => 'nullable|numeric|min:0',
            'description'       => 'nullable|string|max:1000',
            'is_active'         => 'nullable|boolean',
            'days'              => 'nullable|array',
            'days.*.day_of_week'=> 'required_with:days|string|max:30',
            'days.*.start_time' => 'required_with:days|string',
            'days.*.end_time'   => 'required_with:days|string',
            'days.*.room'       => 'nullable|string|max:100',
            'student_ids'       => 'nullable|array',
            'student_ids.*'     => 'exists:users,id',
        ]);

        $updatedGroup = $this->groupService->updateGroup($group, $validated);

        return $this->successResponse(
            new GroupResource($updatedGroup),
            'تم تحديث بيانات المجموعة بنجاح / Group updated successfully'
        );
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $group = Group::where('tenant_id', $tenant->id)->findOrFail($id);

        $this->groupService->deleteGroup($group);

        return $this->successResponse(null, 'تم حذف المجموعة الدراسية بنجاح / Group deleted successfully');
    }

    /**
     * Toggle group active status.
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $group = Group::where('tenant_id', $tenant->id)->findOrFail($id);

        $updated = $this->groupService->toggleStatus($group);

        return $this->successResponse(
            new GroupResource($updated),
            'تم تغيير حالة المجموعة بنجاح / Group status toggled successfully'
        );
    }

    /**
     * Add student to group (group_users).
     */
    public function addStudent(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $group = Group::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status'  => 'nullable|string|in:active,paused,left',
            'notes'   => 'nullable|string|max:500',
        ]);

        $updated = $this->groupService->addStudent($group, $validated['user_id'], $validated);

        return $this->successResponse(
            new GroupResource($updated),
            'تم إضافة الطالب إلى المجموعة بنجاح / Student added to group successfully'
        );
    }

    /**
     * Remove student from group.
     */
    public function removeStudent(Request $request, int $id, int $userId): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $group = Group::where('tenant_id', $tenant->id)->findOrFail($id);

        $updated = $this->groupService->removeStudent($group, $userId);

        return $this->successResponse(
            new GroupResource($updated),
            'تم إزالة الطالب من المجموعة بنجاح / Student removed from group successfully'
        );
    }
}
