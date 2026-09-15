<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreStudentRequest;
use App\Http\Requests\Tenant\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\User;
use App\Services\Tenant\TenantStudentService;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantStudentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TenantManager $tenantManager,
        protected TenantStudentService $studentService
    ) {
    }

    /**
     * Display listing of registered students.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $students = $this->studentService->getStudents($tenant, $request);

        return $this->successResponse(
            StudentResource::collection($students),
            'تم جلب قائمة الطلاب بنجاح / Students list retrieved successfully'
        );
    }

    /**
     * Store new student.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $student = $this->studentService->storeStudent($tenant, $request->validated());

        return $this->createdResponse(
            new StudentResource($student->load('grade.stage')),
            'تم إضافة الطالب بنجاح! / Student created successfully!'
        );
    }

    /**
     * Display single student details.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $student = $this->studentService->showStudent($tenant, $id);

        return $this->successResponse(new StudentResource($student));
    }

    /**
     * Update student details.
     */
    public function update(UpdateStudentRequest $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $student = User::where('tenant_id', $tenant->id)
            ->where('role', User::ROLE_STUDENT)
            ->findOrFail($id);

        $updatedStudent = $this->studentService->updateStudent($student, $request->validated());

        return $this->successResponse(
            new StudentResource($updatedStudent->load('grade.stage')),
            'تم تحديث بيانات الطالب بنجاح / Student updated successfully'
        );
    }

    /**
     * Delete student profile image file.
     */
    public function deleteImage(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $student = User::where('tenant_id', $tenant->id)
            ->where('role', User::ROLE_STUDENT)
            ->findOrFail($id);

        $updatedStudent = $this->studentService->deleteImage($student);

        return $this->successResponse(
            new StudentResource($updatedStudent->load('grade.stage')),
            'تم حذف صورة الطالب بنجاح / Student image deleted successfully'
        );
    }

    /**
     * Toggle student active status (is_active).
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $student = User::where('tenant_id', $tenant->id)
            ->where('role', User::ROLE_STUDENT)
            ->findOrFail($id);

        $updatedStudent = $this->studentService->toggleStatus($student);

        return $this->successResponse(
            new StudentResource($updatedStudent->load('grade.stage')),
            'تم تغيير حالة الطالب بنجاح / Student status toggled successfully'
        );
    }

    /**
     * Delete student.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $student = User::where('tenant_id', $tenant->id)
            ->where('role', User::ROLE_STUDENT)
            ->findOrFail($id);

        $this->studentService->deleteStudent($student);

        return $this->successResponse(null, 'تم حذف الطالب بنجاح / Student deleted successfully');
    }
}
