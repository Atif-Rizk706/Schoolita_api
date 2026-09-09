<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\EducationalStage;
use App\Models\Grade;
use App\Models\Package;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicPortalController extends Controller
{
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
            return response()->json([
                'success' => false,
                'message' => 'Center/Tenant not found.'
            ], 404);
        }

        $stages = EducationalStage::with(['grades' => function ($q) {
            $q->where('is_active', true)->withCount('subjects');
        }])->where('is_active', true)->get();

        $featuredSubjects = Subject::with(['grade.stage', 'teachers'])
            ->where('is_active', true)
            ->take(8)
            ->get();

        $featuredTeachers = Teacher::with(['subjects.grade'])
            ->where('is_active', true)
            ->take(8)
            ->get();

        $testimonials = Testimonial::where('is_active', true)->get();

        $packages = Package::with('features')
            ->where('is_active', true)
            ->take(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tenant' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'slug' => $tenant->slug,
                    'logo' => $tenant->logo,
                    'cover_image' => $tenant->cover_image,
                    'primary_color' => $tenant->primary_color,
                    'secondary_color' => $tenant->secondary_color,
                    'phone' => $tenant->phone,
                    'whatsapp' => $tenant->whatsapp,
                    'email' => $tenant->email,
                    'address' => $tenant->address,
                    'working_hours' => $tenant->working_hours,
                    'social_links' => $tenant->social_links,
                    'hero_title' => $tenant->hero_title,
                    'hero_subtitle' => $tenant->hero_subtitle,
                    'stats' => $tenant->stats ?: [
                        'students_count' => '+15,000',
                        'subjects_count' => '+12',
                        'teachers_count' => '+50',
                        'satisfaction_rate' => '99%',
                    ],
                ],
                'stages' => $stages,
                'featured_subjects' => $featuredSubjects,
                'featured_teachers' => $featuredTeachers,
                'packages' => $packages,
                'testimonials' => $testimonials,
            ]
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

        return response()->json([
            'success' => true,
            'data' => $stages
        ]);
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

        return response()->json([
            'success' => true,
            'data' => $subjects
        ]);
    }

    /**
     * Get single subject details.
     */
    public function subjectDetail($id): JsonResponse
    {
        $subject = Subject::with(['grade.stage', 'teachers', 'packages.features'])
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $subject
        ]);
    }

    /**
     * Get list of teachers with filters.
     */
    public function teachers(Request $request): JsonResponse
    {
        $query = Teacher::with(['subjects.grade.stage'])
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

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    /**
     * Get single teacher profile details.
     */
    public function teacherDetail($id): JsonResponse
    {
        $teacher = Teacher::with(['subjects.grade.stage'])
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }

    /**
     * Get packages list.
     */
    public function packages(Request $request): JsonResponse
    {
        $query = Package::with(['features', 'subject', 'grade'])
            ->where('is_active', true);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $packages = $query->get();

        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }

    /**
     * Get data formatted specifically for the 3-step Booking Wizard.
     */
    public function bookingData(): JsonResponse
    {
        $subjects = Subject::with(['teachers' => function ($q) {
            $q->where('is_active', true);
        }, 'grade.stage'])
            ->where('is_active', true)
            ->get();

        $packages = Package::with('features')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'subjects' => $subjects,
                'packages' => $packages,
            ]
        ]);
    }

    /**
     * Store new student booking from the wizard.
     */
    public function storeBooking(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'package_id' => 'required|exists:packages,id',
            'student_name' => 'required|string|max:150',
            'student_phone' => 'required|string|max:30',
            'parent_phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $booking = Booking::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تقديم طلب الحجز بنجاح! وسنتواصل معك قريباً لتأكيد المواعيد.',
            'data' => $booking
        ], 201);
    }

    /**
     * Store contact inquiry message.
     */
    public function storeContact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:30',
            'message' => 'required|string|max:1000',
        ]);

        $message = ContactMessage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رسالتك بنجاح! وسيقوم فريق الدعم بالرد عليك قريباً.',
            'data' => $message
        ], 201);
    }
}
