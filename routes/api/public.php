<?php

use App\Http\Controllers\Api\PublicPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Portal API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('public')->group(function () {
    // Complete Landing Page Data (Identity, Stats, Featured items, Testimonials)
    Route::get('/landing', [PublicPortalController::class, 'landing']);

    // Educational Stages & Grades Tree
    Route::get('/stages-and-grades', [PublicPortalController::class, 'stagesAndGrades']);

    // Subjects Directory (Filter by stage, grade, or search)
    Route::get('/subjects', [PublicPortalController::class, 'subjects']);
    Route::get('/subjects/{id}', [PublicPortalController::class, 'subjectDetail']);

    // Teachers Directory (Filter by stage, subject, or search)
    Route::get('/teachers', [PublicPortalController::class, 'teachers']);
    Route::get('/teachers/{id}', [PublicPortalController::class, 'teacherDetail']);

    // Curriculum Overview for a Subject-Teacher assignment
    Route::get('/curriculum/{subjectTeacherId}', [PublicPortalController::class, 'curriculum']);

    // Subscription Data & Submission
    Route::get('/subscription-data', [PublicPortalController::class, 'subscriptionData']);
    Route::post('/subscriptions', [PublicPortalController::class, 'storeSubscription']);

    // Contact Form Submission
    Route::post('/contact', [PublicPortalController::class, 'storeContact']);
});
