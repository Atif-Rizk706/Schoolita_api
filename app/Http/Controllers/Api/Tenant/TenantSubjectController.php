<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreSubjectRequest;
use App\Http\Requests\Tenant\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Services\Tenant\TenantSubjectService;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantSubjectController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TenantManager $tenantManager,
        protected TenantSubjectService $subjectService
    ) {
    }

    /**
     * Display listing of subjects.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subjects = $this->subjectService->getSubjects($tenant, $request);

        return $this->successResponse(
            SubjectResource::collection($subjects),
            'تم جلب قائمة المواد الدراسية بنجاح / Subjects list retrieved successfully'
        );
    }

    /**
     * Store new subject.
     */
    public function store(StoreSubjectRequest $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subject = $this->subjectService->storeSubject($tenant, $request->validated());

        return $this->createdResponse(
            new SubjectResource($subject->load(['grade.stage', 'teachers', 'outcomes', 'features'])),
            'تم إضافة المادة الدراسية بنجاح! / Subject created successfully!'
        );
    }

    /**
     * Display single subject.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subject = Subject::with(['grade.stage', 'teachers', 'outcomes', 'features'])->where('tenant_id', $tenant->id)->findOrFail($id);

        return $this->successResponse(new SubjectResource($subject));
    }

    /**
     * Update subject.
     */
    public function update(UpdateSubjectRequest $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subject = Subject::where('tenant_id', $tenant->id)->findOrFail($id);

        $updatedSubject = $this->subjectService->updateSubject($subject, $request->validated());

        return $this->successResponse(
            new SubjectResource($updatedSubject->load(['grade.stage', 'teachers'])),
            'تم تحديث المادة الدراسية بنجاح / Subject updated successfully'
        );
    }

    /**
     * Toggle subject active status (is_active).
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subject = Subject::where('tenant_id', $tenant->id)->findOrFail($id);

        $updatedSubject = $this->subjectService->toggleStatus($subject);

        return $this->successResponse(
            new SubjectResource($updatedSubject->load(['grade.stage', 'teachers'])),
            'تم تغيير حالة المادة الدراسية بنجاح / Subject status toggled successfully'
        );
    }

    /**
     * Delete subject.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $subject = Subject::where('tenant_id', $tenant->id)->findOrFail($id);
        $this->subjectService->deleteSubject($subject);

        return $this->successResponse(null, 'تم حذف المادة بنجاح / Subject deleted successfully');
    }
}
