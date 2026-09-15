<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use App\Models\Unit;
use App\Services\BunnyStreamService;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherContentController extends Controller
{
    public function __construct(
        protected TenantManager $tenantManager,
        protected BunnyStreamService $bunnyStream
    ) {
    }

    /**
     * Resolve the target teacher model for current request (User -> Teacher mapping).
     */
    protected function resolveTeacher(Request $request): ?Teacher
    {
        $user = $request->user();

        if ($user->isSuperAdmin() || $user->isTenantAdmin()) {
            if ($request->has('teacher_id')) {
                return Teacher::find($request->teacher_id);
            }
        }

        return Teacher::where('user_id', $user->id)->first();
    }

    /**
     * Get all subject assignments (Subject + Grade + Stage) for current teacher.
     */
    public function myAssignments(Request $request): JsonResponse
    {
        $teacher = $this->resolveTeacher($request);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'حساب المعلم غير موجود أو غير مرتبط بملف معلم في هذا السنتر.'
            ], 404);
        }

        $assignments = SubjectTeacher::with(['subject.grade.stage', 'units' => function ($q) {
            $q->withCount('lessons');
        }])
            ->where('teacher_id', $teacher->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => $teacher,
                'assignments' => $assignments,
            ]
        ]);
    }

    /**
     * Get all units & lessons for a specific subject-teacher assignment.
     */
    public function units(Request $request, int $subjectTeacherId): JsonResponse
    {
        $teacher = $this->resolveTeacher($request);

        $assignment = SubjectTeacher::with(['subject.grade.stage', 'teacher'])
            ->findOrFail($subjectTeacherId);

        if ($teacher && $assignment->teacher_id !== $teacher->id && !$request->user()->isSuperAdmin() && !$request->user()->isTenantAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإدارة محتوى هذا المعلم.'
            ], 403);
        }

        $units = Unit::with(['lessons' => function ($q) {
            $q->orderBy('sort_order');
        }])
            ->where('subject_teacher_id', $subjectTeacherId)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'assignment' => $assignment,
                'units' => $units
            ]
        ]);
    }

    /**
     * Store a new unit.
     */
    public function storeUnit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_teacher_id' => 'required|exists:subject_teacher,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $assignment = SubjectTeacher::findOrFail($validated['subject_teacher_id']);
        $teacher = $this->resolveTeacher($request);

        if ($teacher && $assignment->teacher_id !== $teacher->id && !$request->user()->isSuperAdmin() && !$request->user()->isTenantAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإضافة وحدات لهذه المادة.'
            ], 403);
        }

        $unit = Unit::create([
            'tenant_id' => $assignment->subject->tenant_id ?? $this->tenantManager->getTenantId(),
            'subject_teacher_id' => $assignment->id,
            'subject_id' => $assignment->subject_id,
            'teacher_id' => $assignment->teacher_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? (Unit::where('subject_teacher_id', $assignment->id)->max('sort_order') + 1),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الوحدة الدراسية بنجاح!',
            'data' => $unit
        ], 201);
    }

    /**
     * Update an existing unit.
     */
    public function updateUnit(Request $request, int $id): JsonResponse
    {
        $unit = Unit::findOrFail($id);
        $teacher = $this->resolveTeacher($request);

        if ($teacher && $unit->teacher_id !== $teacher->id && !$request->user()->isSuperAdmin() && !$request->user()->isTenantAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذه الوحدة.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $unit->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الوحدة الدراسية بنجاح.',
            'data' => $unit
        ]);
    }

    /**
     * Delete a unit and its lessons.
     */
    public function deleteUnit(Request $request, int $id): JsonResponse
    {
        $unit = Unit::findOrFail($id);
        $teacher = $this->resolveTeacher($request);

        if ($teacher && $unit->teacher_id !== $teacher->id && !$request->user()->isSuperAdmin() && !$request->user()->isTenantAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذه الوحدة.'
            ], 403);
        }

        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الوحدة وجميع دروسها التابعة بنجاح.'
        ]);
    }

    /**
     * Store a new lesson inside a unit.
     */
    public function storeLesson(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:bunny_video,pdf,live,text,quiz',
            'bunny_video_id' => 'nullable|string|max:255',
            'bunny_library_id' => 'nullable|string|max:255',
            'video_url' => 'nullable|string',
            'pdf_url' => 'nullable|string',
            'duration_minutes' => 'nullable|integer',
            'is_free_preview' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);
        $teacher = $this->resolveTeacher($request);

        if ($teacher && $unit->teacher_id !== $teacher->id && !$request->user()->isSuperAdmin() && !$request->user()->isTenantAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإضافة دروس لهذه الوحدة.'
            ], 403);
        }

        $embedUrl = null;
        if (!empty($validated['bunny_video_id'])) {
            $embedUrl = $this->bunnyStream->generateEmbedUrl($validated['bunny_video_id']);
        } elseif (!empty($validated['video_url'])) {
            $embedUrl = $validated['video_url'];
        }

        $lesson = Lesson::create([
            'tenant_id' => $unit->tenant_id,
            'unit_id' => $unit->id,
            'subject_teacher_id' => $unit->subject_teacher_id,
            'subject_id' => $unit->subject_id,
            'teacher_id' => $unit->teacher_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'bunny_video_id' => $validated['bunny_video_id'] ?? null,
            'bunny_library_id' => $validated['bunny_library_id'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'embed_url' => $embedUrl,
            'pdf_url' => $validated['pdf_url'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? 0,
            'is_free_preview' => $validated['is_free_preview'] ?? false,
            'sort_order' => $validated['sort_order'] ?? (Lesson::where('unit_id', $unit->id)->max('sort_order') + 1),
            'is_published' => $validated['is_published'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الدرس بنجاح! 🎉',
            'data' => $lesson
        ], 201);
    }

    /**
     * Update an existing lesson.
     */
    public function updateLesson(Request $request, int $id): JsonResponse
    {
        $lesson = Lesson::findOrFail($id);
        $teacher = $this->resolveTeacher($request);

        if ($teacher && $lesson->teacher_id !== $teacher->id && !$request->user()->isSuperAdmin() && !$request->user()->isTenantAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا الدرس.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|in:bunny_video,pdf,live,text,quiz',
            'bunny_video_id' => 'nullable|string|max:255',
            'bunny_library_id' => 'nullable|string|max:255',
            'video_url' => 'nullable|string',
            'pdf_url' => 'nullable|string',
            'duration_minutes' => 'nullable|integer',
            'is_free_preview' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ]);

        if (isset($validated['bunny_video_id']) && !empty($validated['bunny_video_id'])) {
            $validated['embed_url'] = $this->bunnyStream->generateEmbedUrl($validated['bunny_video_id']);
        }

        $lesson->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الدرس بنجاح.',
            'data' => $lesson
        ]);
    }

    /**
     * Delete a lesson.
     */
    public function deleteLesson(Request $request, int $id): JsonResponse
    {
        $lesson = Lesson::findOrFail($id);
        $teacher = $this->resolveTeacher($request);

        if ($teacher && $lesson->teacher_id !== $teacher->id && !$request->user()->isSuperAdmin() && !$request->user()->isTenantAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذا الدرس.'
            ], 403);
        }

        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الدرس بنجاح.'
        ]);
    }

    /**
     * Generate Direct Upload URL for Bunny Stream.
     */
    public function generateUploadUrl(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        $title = $request->input('title', 'Lesson Video Upload');
        $uploadData = $this->bunnyStream->generateUploadSignature($title);

        return response()->json([
            'success' => true,
            'message' => 'تم توليد بيانات وتوقيع الرفع المباشر لـ Bunny Stream بنجاح.',
            'data' => $uploadData
        ]);
    }

    /**
     * Batch reorder units or lessons.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'units' => 'nullable|array',
            'units.*.id' => 'required|exists:units,id',
            'units.*.sort_order' => 'required|integer',
            'lessons' => 'nullable|array',
            'lessons.*.id' => 'required|exists:lessons,id',
            'lessons.*.sort_order' => 'required|integer',
        ]);

        if ($request->has('units')) {
            foreach ($request->units as $item) {
                Unit::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
        }

        if ($request->has('lessons')) {
            foreach ($request->lessons as $item) {
                Lesson::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث ترتيب الوحدات والدروس بنجاح.'
        ]);
    }
}
