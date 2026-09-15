<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTeacherRequest;
use App\Http\Requests\Tenant\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use App\Services\Tenant\TenantTeacherService;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantTeacherController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TenantManager $tenantManager,
        protected TenantTeacherService $teacherService
    ) {
    }

    /**
     * Display listing of teachers.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $teachers = $this->teacherService->getTeachers($tenant, $request);

        return $this->successResponse(
            TeacherResource::collection($teachers),
            'تم جلب قائمة المعلمين بنجاح / Teachers list retrieved successfully'
        );
    }

    /**
     * Store new teacher.
     */
    public function store(StoreTeacherRequest $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $teacher = $this->teacherService->storeTeacher($tenant, $request->validated());

        return $this->createdResponse(
            new TeacherResource($teacher->load(['subjects.grade.stage', 'features'])),
            'تم إضافة المعلم بنجاح! / Teacher created successfully!'
        );
    }

    /**
     * Display teacher details.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $teacher = Teacher::with(['subjects.grade.stage', 'features'])->where('tenant_id', $tenant->id)->findOrFail($id);

        return $this->successResponse(new TeacherResource($teacher));
    }

    /**
     * Update teacher.
     */
    public function update(UpdateTeacherRequest $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $teacher = Teacher::where('tenant_id', $tenant->id)->findOrFail($id);

        $updatedTeacher = $this->teacherService->updateTeacher($teacher, $request->validated());

        return $this->successResponse(
            new TeacherResource($updatedTeacher->load(['subjects.grade.stage', 'features'])),
            'تم تحديث بيانات المعلم بنجاح / Teacher updated successfully'
        );
    }

    /**
     * Delete teacher avatar file.
     */
    public function deleteAvatar(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $teacher = Teacher::where('tenant_id', $tenant->id)->findOrFail($id);

        $updatedTeacher = $this->teacherService->deleteAvatar($teacher);

        return $this->successResponse(
            new TeacherResource($updatedTeacher->load(['subjects.grade.stage', 'features'])),
            'تم حذف صورة المعلم بنجاح / Teacher avatar deleted successfully'
        );
    }

    /**
     * Toggle teacher active status (is_active).
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $teacher = Teacher::where('tenant_id', $tenant->id)->findOrFail($id);

        $updatedTeacher = $this->teacherService->toggleStatus($teacher);

        return $this->successResponse(
            new TeacherResource($updatedTeacher->load(['subjects.grade.stage', 'features'])),
            'تم تغيير حالة المعلم بنجاح / Teacher status toggled successfully'
        );
    }

    /**
     * Delete teacher.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $teacher = Teacher::where('tenant_id', $tenant->id)->findOrFail($id);
        $this->teacherService->deleteTeacher($teacher);

        return $this->successResponse(null, 'تم حذف المعلم بنجاح / Teacher deleted successfully');
    }
}
