<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Scolita Multi-Tenant SaaS
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ----------------------------------------------------
    // Public Portal Routes (No Authentication Required)
    // Matches React Website: Landing, Subjects, Teachers, Booking, Contact
    // ----------------------------------------------------
    Route::prefix('public')->group(function () {
        // Complete Landing Page Data (Identity, Stats, Featured items, Testimonials)
        Route::get('/landing', [PublicPortalController::class, 'landing']);

        // Educational Stages & Grades Tree (ابتدائي، إعدادي، ثانوي وصفوفهم)
        Route::get('/stages-and-grades', [PublicPortalController::class, 'stagesAndGrades']);

        // Subjects Directory (Filter by stage, grade, or search)
        Route::get('/subjects', [PublicPortalController::class, 'subjects']);
        Route::get('/subjects/{id}', [PublicPortalController::class, 'subjectDetail']);

        // Teachers Directory (Filter by stage, subject, or search)
        Route::get('/teachers', [PublicPortalController::class, 'teachers']);
        Route::get('/teachers/{id}', [PublicPortalController::class, 'teacherDetail']);

        // Packages & Pricing Plans (Online & Center)
        Route::get('/packages', [PublicPortalController::class, 'packages']);

        // Dynamic 3-Step Booking Wizard Data & Submission
        Route::get('/booking-data', [PublicPortalController::class, 'bookingData']);
        Route::post('/bookings', [PublicPortalController::class, 'storeBooking']);

        // Contact Form Submission
        Route::post('/contact', [PublicPortalController::class, 'storeContact']);
    });

    // ----------------------------------------------------
    // Authentication Routes (Students, Teachers, Center Admins)
    // ----------------------------------------------------
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Protected Auth Routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

});
