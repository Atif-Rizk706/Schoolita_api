<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenantResource;
use App\Http\Resources\UserResource;
use App\Services\Tenant\TenantAuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantAuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected TenantAuthService $authService)
    {
    }

    /**
     * Login for Tenant Admin owner.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login($validated['email'], $validated['password']);

        return $this->successResponse([
            'user'       => new UserResource($result['user']),
            'tenant'     => new TenantResource($result['tenant']),
            'token'      => $result['token'],
            'token_type' => $result['token_type'],
        ], 'تم تسجيل دخول مدير السنتر بنجاح / Logged in successfully');
    }

    /**
     * Register a new educational center (Tenant) & tenant admin user.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'slug'              => 'required|string|max:100|unique:tenants,slug|alpha_dash',
            'domain'            => 'nullable|string|max:255|unique:tenants,domain',
            'phone'             => 'required|string|max:30',
            'whatsapp'          => 'nullable|string|max:30',
            'email'             => 'required|email|max:150',
            'address'           => 'nullable|string|max:255',
            'primary_color'     => 'nullable|string|max:20',
            'secondary_color'   => 'nullable|string|max:20',
            'hero_title'        => 'nullable|string|max:255',
            'hero_subtitle'     => 'nullable|string|max:500',
            'subscription_plan' => 'nullable|string|in:basic,standard,pro',
            'admin_name'        => 'required|string|max:150',
            'admin_email'       => 'required|email|max:150|unique:users,email',
            'admin_password'    => 'required|string|min:6',
        ]);

        $result = $this->authService->register($validated);

        return $this->createdResponse([
            'tenant'     => new TenantResource($result['tenant']),
            'admin'      => new UserResource($result['admin']),
            'token'      => $result['token'],
            'token_type' => $result['token_type'],
        ], 'تم إنشاء حساب السنتر والمدير بنجاح! مرحباً بك في Scolita 🎉 / Center and admin registered successfully');
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('tenant');

        return $this->successResponse([
            'user'   => new UserResource($user),
            'tenant' => new TenantResource($user->tenant),
        ]);
    }

    /**
     * Logout and revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح / Logged out successfully');
    }
}
