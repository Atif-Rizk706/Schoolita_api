<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Master API Routes Loader - Scolita Multi-Tenant SaaS
|--------------------------------------------------------------------------
| Clean, modular route structure separated by domain/feature area.
| Includes: Public Portal, Auth, Teacher Content, and Tenant Admin.
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/public.php';
    require __DIR__ . '/api/auth.php';
    require __DIR__ . '/api/teacher.php';
    require __DIR__ . '/api/tenant.php';
});
