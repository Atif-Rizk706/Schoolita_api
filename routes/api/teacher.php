<?php

use App\Http\Controllers\Api\Teacher\TeacherContentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Teacher Content Management API Routes (Units & Lessons)
|--------------------------------------------------------------------------
*/

Route::prefix('teacher')->middleware('auth:sanctum')->group(function () {
    Route::get('/my-assignments', [TeacherContentController::class, 'myAssignments']);
    Route::get('/assignments/{subjectTeacherId}/units', [TeacherContentController::class, 'units']);

    // Unit Management
    Route::post('/units', [TeacherContentController::class, 'storeUnit']);
    Route::put('/units/{id}', [TeacherContentController::class, 'updateUnit']);
    Route::delete('/units/{id}', [TeacherContentController::class, 'deleteUnit']);

    // Lesson Management
    Route::post('/lessons', [TeacherContentController::class, 'storeLesson']);
    Route::put('/lessons/{id}', [TeacherContentController::class, 'updateLesson']);
    Route::delete('/lessons/{id}', [TeacherContentController::class, 'deleteLesson']);

    // Direct Video Upload (Bunny Stream) & Reorder
    Route::post('/lessons/upload-url', [TeacherContentController::class, 'generateUploadUrl']);
    Route::post('/reorder', [TeacherContentController::class, 'reorder']);
});
