<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    /**
     * Register a new student account under current tenant.
     */
    public function register(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'parent_email' => 'nullable|email|max:150',
            'parent_phone' => 'nullable|string|max:30',
            'parent_whatsapp' => 'nullable|string|max:30',
            'country' => 'nullable|string|max:10',
            'image' => 'nullable|string|max:255',
            'grade_id' => 'nullable|exists:grades,id',
        ]);

        $user = User::create([
            'tenant_id' => $tenant?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_STUDENT,
            'phone' => $validated['phone'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'parent_email' => $validated['parent_email'] ?? null,
            'parent_phone' => $validated['parent_phone'] ?? null,
            'parent_whatsapp' => $validated['parent_whatsapp'] ?? null,
            'country' => $validated['country'] ?? 'EG',
            'image' => $validated['image'] ?? null,
            'grade_id' => $validated['grade_id'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء حساب الطالب بنجاح! مرحباً بك في المنصة 🎉',
            'data' => [
                'user' => $user->load('grade.stage'),
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Login for student, teacher, or center admin.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة.'],
            ]);
        }

        $activeTenant = $this->tenantManager->getTenant();

        // If user is not super admin, check if user belongs to this tenant
        if (!$user->isSuperAdmin() && $activeTenant && $user->tenant_id !== $activeTenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الحساب غير مسجل في هذا السنتر التعليمي.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح!',
            'data' => [
                'user' => $user->load(['tenant', 'grade.stage']),
                'token' => $token,
                'token_type' => 'Bearer',
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Get profile of currently authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load(['tenant', 'grade.stage'])
        ]);
    }

    /**
     * Logout and revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح.'
        ]);
    }
}
