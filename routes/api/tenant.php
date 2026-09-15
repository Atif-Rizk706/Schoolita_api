<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Tenant\TenantAuthController;
use App\Http\Controllers\Api\Tenant\TenantProfileController;
use App\Http\Controllers\Api\Tenant\TenantTeacherController;
use App\Http\Controllers\Api\Tenant\TenantSubjectController;
use App\Http\Controllers\Api\Tenant\TenantSubscriptionController;
use App\Http\Controllers\Api\Tenant\TenantStudentController;
use App\Http\Controllers\Api\Tenant\TenantContactController;
use App\Http\Controllers\Api\Tenant\StageController;
use App\Http\Controllers\Api\Tenant\GradeController;

/*
|--------------------------------------------------------------------------
| Tenant Admin Dashboard API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('tenant')->group(function () {

    // ── Public: Auth ──────────────────────────────────────────────────────
    Route::post('/login',    [TenantAuthController::class, 'login']);
    Route::post('/register', [TenantAuthController::class, 'register']);

    // ── Protected: Tenant Admin Dashboard ─────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [TenantAuthController::class, 'logout']);
        Route::get('/me',      [TenantAuthController::class, 'me']);

        // Dashboard Stats
        Route::get('/dashboard',       [TenantProfileController::class, 'dashboardStats']);
        Route::get('/dashboard-stats', [TenantProfileController::class, 'dashboardStats']);

        // Profile
        Route::get('/profile',  [TenantProfileController::class, 'show']);
        Route::post('/profile', [TenantProfileController::class, 'update']); // POST for file uploads

        // Educational Structure
        Route::apiResource('stages', StageController::class);
        Route::apiResource('stages.grades', GradeController::class)->shallow();

        // Resources
        Route::apiResource('teachers',      TenantTeacherController::class);
        Route::delete('teachers/{teacher}/avatar',        [TenantTeacherController::class, 'deleteAvatar']);
        Route::patch('teachers/{teacher}/toggle-status',  [TenantTeacherController::class, 'toggleStatus']);

        Route::apiResource('subjects',      TenantSubjectController::class);
        Route::patch('subjects/{subject}/toggle-status',  [TenantSubjectController::class, 'toggleStatus']);

        Route::apiResource('subscriptions', TenantSubscriptionController::class);

        Route::apiResource('students',      TenantStudentController::class);
        Route::delete('students/{student}/image',         [TenantStudentController::class, 'deleteImage']);
        Route::patch('students/{student}/toggle-status',  [TenantStudentController::class, 'toggleStatus']);

        Route::apiResource('contacts',      TenantContactController::class);
    });
});
