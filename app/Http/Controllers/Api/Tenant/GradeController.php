<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationalStageResource;
use App\Http\Resources\GradeResource;
use App\Models\EducationalStage;
use App\Models\Grade;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GradeController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    /**
     * Get all grades for a specific stage.
     */
    public function index(Request $request, int $stageId): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        $stage = EducationalStage::where('tenant_id', $tenant->id)->findOrFail($stageId);

        $grades = Grade::where('tenant_id', $tenant->id)
            ->where('stage_id', $stage->id)
            ->orderBy('order')
            ->withCount(['subjects', 'students'])
            ->get();

        return response()->json([
            'success' => true,
            'stage'   => new EducationalStageResource($stage),
            'data'    => GradeResource::collection($grades),
        ]);
    }

    /**
     * Add a new grade to a stage.
     */
    public function store(Request $request, int $stageId): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        $stage = EducationalStage::where('tenant_id', $tenant->id)->findOrFail($stageId);

        $validated = $request->validate([
            'name'      => 'required',
            'slug'      => 'nullable|string|max:60|unique:grades,slug',
            'code'      => 'nullable|string|max:20',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $nameStr = is_array($validated['name']) ? ($validated['name']['en'] ?? reset($validated['name'])) : $validated['name'];

        $grade = Grade::create([
            'tenant_id' => $tenant->id,
            'stage_id'  => $stage->id,
            'name'      => $validated['name'],
            'slug'      => $validated['slug'] ?? Str::slug($nameStr),
            'code'      => $validated['code'] ?? null,
            'order'     => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return $this->createdResponse(new GradeResource($grade), 'تم إضافة الصف الدراسي بنجاح! / Grade created successfully!');
    }

    /**
     * Get grade details with subjects and students count.
     */
    public function show(Request $request, int $stageId, int $gradeId): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        // Ensure stage belongs to tenant
        EducationalStage::where('tenant_id', $tenant->id)->findOrFail($stageId);

        $grade = Grade::where('tenant_id', $tenant->id)
            ->where('stage_id', $stageId)
            ->withCount(['subjects', 'students'])
            ->with(['subjects.teachers', 'stage'])
            ->findOrFail($gradeId);

        return $this->successResponse(new GradeResource($grade));
    }

    /**
     * Update a grade.
     */
    public function update(Request $request, int $stageId, int $gradeId): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        EducationalStage::where('tenant_id', $tenant->id)->findOrFail($stageId);

        $grade = Grade::where('tenant_id', $tenant->id)
            ->where('stage_id', $stageId)
            ->findOrFail($gradeId);

        $validated = $request->validate([
            'name'      => 'sometimes|required',
            'slug'      => 'nullable|string|max:60|unique:grades,slug,' . $gradeId,
            'code'      => 'nullable|string|max:20',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $grade->update($validated);

        return $this->successResponse(new GradeResource($grade->fresh(['stage', 'subjects'])), 'تم تحديث الصف الدراسي بنجاح / Grade updated successfully');
    }

    /**
     * Toggle grade active status (is_active).
     */
    public function toggleStatus(Request $request, int $stageId, int $gradeId): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        EducationalStage::where('tenant_id', $tenant->id)->findOrFail($stageId);

        $grade = Grade::where('tenant_id', $tenant->id)
            ->where('stage_id', $stageId)
            ->findOrFail($gradeId);

        $grade->update(['is_active' => !$grade->is_active]);

        return $this->successResponse(new GradeResource($grade->fresh(['stage', 'subjects'])), 'تم تغيير حالة الصف بنجاح / Grade status toggled successfully');
    }

    /**
     * Delete a grade (only if no subjects or students).
     */
    public function destroy(Request $request, int $stageId, int $gradeId): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        EducationalStage::where('tenant_id', $tenant->id)->findOrFail($stageId);

        $grade = Grade::where('tenant_id', $tenant->id)
            ->where('stage_id', $stageId)
            ->withCount(['subjects', 'students'])
            ->findOrFail($gradeId);

        if ($grade->subjects_count > 0 || $grade->students_count > 0) {
            return $this->validationErrorResponse(
                ['subjects_or_students' => ['الصف يحتوي على مواد أو طلاب']],
                'لا يمكن حذف الصف لأنه يحتوي على مواد دراسية أو طلاب مرتبطين به / Cannot delete grade with existing subjects or students'
            );
        }

        $grade->delete();

        return $this->successResponse(null, 'تم حذف الصف الدراسي بنجاح / Grade deleted successfully');
    }
}
