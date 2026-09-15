<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Http\Resources\EducationalStageResource;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\SubjectTeacherResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\TenantResource;
use App\Http\Resources\TestimonialResource;
use App\Http\Resources\UnitResource;
use App\Models\ContactMessage;
use App\Models\EducationalStage;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\Unit;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicPortalController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected TenantManager $tenantManager)
    {
    }

    /**
     * Get complete public landing page data.
     */
    public function landing(): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant();

        if (!$tenant) {
            return $this->notFoundResponse('Center/Tenant not found.');
        }

        $stages = EducationalStage::with(['grades' => function ($q) {
            $q->where('is_active', true)->withCount('subjects');
        }])->where('is_active', true)->get();

        $featuredSubjects = Subject::with(['grade.stage', 'teachers'])
            ->where('is_active', true)
            ->take(8)
            ->get();

        $featuredTeachers = Teacher::with(['subjects.grade', 'features'])
            ->where('is_active', true)
            ->take(8)
            ->get();

        $testimonials = Testimonial::where('is_active', true)->get();

        return $this->successResponse([
            'tenant'            => new TenantResource($tenant),
            'stages'            => EducationalStageResource::collection($stages),
            'featured_subjects' => SubjectResource::collection($featuredSubjects),
            'featured_teachers' => TeacherResource::collection($featuredTeachers),
            'testimonials'      => TestimonialResource::collection($testimonials),
        ]);
    }

    /**
     * Get educational stages and grades hierarchy.
     */
    public function stagesAndGrades(): JsonResponse
    {
        $stages = EducationalStage::with(['grades' => function ($q) {
            $q->where('is_active', true)->withCount('subjects');
        }])->where('is_active', true)->get();

        return $this->successResponse(EducationalStageResource::collection($stages));
    }

    /**
     * Get list of subjects with filters (by stage, grade, search keyword).
     */
    public function subjects(Request $request): JsonResponse
    {
        $query = Subject::with(['grade.stage', 'teachers'])
            ->where('is_active', true);

        if ($request->filled('stage_id')) {
            $query->whereHas('grade', function ($q) use ($request) {
                $q->where('stage_id', $request->stage_id);
            });
        }

        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $subjects = $query->paginate($request->input('per_page', 12));

        return $this->successResponse(SubjectResource::collection($subjects));
    }

    /**
     * Get single subject details.
     */
    public function subjectDetail($id): JsonResponse
    {
        $subject = Subject::with(['grade.stage', 'teachers'])
            ->where('is_active', true)
            ->findOrFail($id);

        return $this->successResponse(new SubjectResource($subject));
    }

    /**
     * Get list of teachers with filters.
     */
    public function teachers(Request $request): JsonResponse
    {
        $query = Teacher::with(['subjects.grade.stage', 'features'])
            ->where('is_active', true);

        if ($request->filled('subject_id')) {
            $query->whereHas('subjects', function ($q) use ($request) {
                $q->where('subjects.id', $request->subject_id);
            });
        }

        if ($request->filled('stage_id')) {
            $query->whereHas('subjects.grade', function ($q) use ($request) {
                $q->where('stage_id', $request->stage_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        $teachers = $query->paginate($request->input('per_page', 12));

        return $this->successResponse(TeacherResource::collection($teachers));
    }

    /**
     * Get single teacher profile details.
     */
    public function teacherDetail($id): JsonResponse
    {
        $teacher = Teacher::with(['subjects.grade.stage', 'features'])
            ->where('is_active', true)
            ->findOrFail($id);

        return $this->successResponse(new TeacherResource($teacher));
    }

    /**
     * Get data formatted specifically for the Subscription Wizard.
     */
    public function subscriptionData(): JsonResponse
    {
        $subjects = Subject::with(['teachers' => function ($q) {
            $q->where('is_active', true);
        }, 'grade.stage'])
            ->where('is_active', true)
            ->get();

        return $this->successResponse([
            'subjects' => SubjectResource::collection($subjects),
        ]);
    }

    /**
     * Store new student subscription from public wizard or portal.
     */
    public function storeSubscription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'subject_teacher_id' => 'required|exists:subject_teacher,id',
            'payment_method'     => 'nullable|string|max:50',
            'notes'              => 'nullable|string|max:500',
        ]);

        $subjectTeacher = SubjectTeacher::with('subject')->findOrFail($validated['subject_teacher_id']);
        $tenant = $this->tenantManager->getTenant() ?? $request->user()?->tenant;

        $subscription = Subscription::create([
            'tenant_id'          => $tenant?->id ?? $subjectTeacher->subject->tenant_id,
            'user_id'            => $validated['user_id'],
            'subject_teacher_id' => $validated['subject_teacher_id'],
            'price'              => $subjectTeacher->subject->subscription_price ?? 0.00,
            'payment_method'     => $validated['payment_method'] ?? 'cash',
            'status'             => 'pending',
            'notes'              => $validated['notes'] ?? null,
        ]);

        return $this->createdResponse(
            new SubscriptionResource($subscription->load(['user', 'subjectTeacher.subject', 'subjectTeacher.teacher'])),
            'تم تقديم طلب الاشتراك بنجاح! وسنتواصل معك قريباً لتأكيد الاشتراك / Subscription requested successfully'
        );
    }

    /**
     * Store contact inquiry message.
     */
    public function storeContact(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant();

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:100',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:1000',
        ]);

        $validated['tenant_id'] = $tenant?->id;

        $message = ContactMessage::create($validated);

        return $this->createdResponse(
            new ContactMessageResource($message),
            'تم إرسال رسالتك بنجاح! وسيقوم فريق الدعم بالرد عليك قريباً / Message sent successfully'
        );
    }

    /**
     * Get curriculum overview (units & lessons) for a subject-teacher assignment.
     */
    public function curriculum($subjectTeacherId): JsonResponse
    {
        $assignment = SubjectTeacher::with(['subject.grade.stage', 'teacher'])
            ->findOrFail($subjectTeacherId);

        $units = Unit::with(['lessons' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])
            ->where('subject_teacher_id', $subjectTeacherId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse([
            'assignment' => new SubjectTeacherResource($assignment),
            'units'      => UnitResource::collection($units),
        ]);
    }
}
