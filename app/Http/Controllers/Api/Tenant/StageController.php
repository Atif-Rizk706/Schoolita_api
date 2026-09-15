<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationalStageResource;
use App\Models\EducationalStage;
use App\Models\Grade;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StageController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    /**
     * Get all stages with their grades + overall statistics.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        $stages = EducationalStage::where('tenant_id', $tenant->id)
            ->orderBy('order')
            ->withCount(['grades', 'grades as active_grades_count' => fn($q) => $q->where('is_active', true)])
            ->with(['grades' => fn($q) => $q->orderBy('order')])
            ->get();

        $totalGrades    = Grade::where('tenant_id', $tenant->id)->count();
        $activeGrades   = Grade::where('tenant_id', $tenant->id)->where('is_active', true)->count();
        $inactiveGrades = $totalGrades - $activeGrades;

        return response()->json([
            'success' => true,
            'stats' => [
                'total_stages'   => $stages->count(),
                'total_grades'   => $totalGrades,
                'active_grades'  => $activeGrades,
                'inactive_grades'=> $inactiveGrades,
            ],
            'data' => EducationalStageResource::collection($stages),
        ]);
    }

    /**
     * Create a new educational stage.
     */
    public function store(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        $validated = $request->validate([
            'name'        => 'required',
            'description' => 'nullable',
            'slug'        => 'nullable|string|max:60|unique:educational_stages,slug',
            'order'       => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['slug']      = $validated['slug'] ?? \Illuminate\Support\Str::slug(is_array($validated['name']) ? ($validated['name']['en'] ?? reset($validated['name'])) : $validated['name']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $stage = EducationalStage::create($validated);

        return $this->createdResponse(new EducationalStageResource($stage), 'تم إضافة المرحلة بنجاح! / Stage created successfully!');
    }

    /**
     * Get a single stage with its grades.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        $stage = EducationalStage::where('tenant_id', $tenant->id)
            ->withCount(['grades', 'grades as active_grades_count' => fn($q) => $q->where('is_active', true)])
            ->with(['grades' => fn($q) => $q->orderBy('order')])
            ->findOrFail($id);

        return $this->successResponse(new EducationalStageResource($stage));
    }

    /**
     * Update an existing stage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        $stage = EducationalStage::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|required',
            'description' => 'nullable',
            'slug'        => 'nullable|string|max:60|unique:educational_stages,slug,' . $id,
            'order'       => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $stage->update($validated);

        return $this->successResponse(new EducationalStageResource($stage->fresh(['grades'])), 'تم تحديث المرحلة بنجاح / Stage updated successfully');
    }

    /**
     * Toggle stage active status (is_active).
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $stage = EducationalStage::where('tenant_id', $tenant->id)->findOrFail($id);

        $stage->update(['is_active' => !$stage->is_active]);

        return $this->successResponse(new EducationalStageResource($stage->fresh(['grades'])), 'تم تغيير حالة المرحلة بنجاح / Stage status toggled successfully');
    }

    /**
     * Delete a stage (only if it has no grades).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        $stage = EducationalStage::where('tenant_id', $tenant->id)
            ->withCount('grades')
            ->findOrFail($id);

        if ($stage->grades_count > 0) {
            return $this->validationErrorResponse(
                ['grades_count' => ['المرحلة تحتوي على صفوف دراسية']],
                'لا يمكن حذف المرحلة لأنها تحتوي على صفوف دراسية. قم بحذف الصفوف أولاً / Cannot delete stage with existing grades'
            );
        }

        $stage->delete();

        return $this->successResponse(null, 'تم حذف المرحلة بنجاح / Stage deleted successfully');
    }
}
