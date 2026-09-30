<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\JobOrderAssignController;
use App\Http\Controllers\Api\JobOrderController;
use App\Http\Controllers\Api\JobOrderHistoryController;
use App\Http\Controllers\Api\JobOrderMediaController;
use App\Http\Controllers\Api\JobOrderStatusController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\WorkshopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication Route
Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');

// Protected Routes (JWT Auth)
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');

    // Workshops & Users
    Route::apiResource('workshops', WorkshopController::class);
    Route::apiResource('users', UserController::class);

    // Feature 1: استقبال العربية وأمر الشغل (Job Intake)
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('job-orders', JobOrderController::class);

    // Job Order Media (توثيق الصور قبل الشغل)
    Route::get('job-orders/{jobOrder}/media', [JobOrderMediaController::class, 'index'])->name('job-orders.media.index');
    Route::post('job-orders/{jobOrder}/media', [JobOrderMediaController::class, 'store'])->name('job-orders.media.store');
    Route::delete('job-order-media/{media}', [JobOrderMediaController::class, 'destroy'])->name('job-orders.media.destroy');

    // Feature 2: تتبع سير العمل (Workflow / Job Status)
    Route::patch('job-orders/{jobOrder}/status', [JobOrderStatusController::class, 'update'])->name('job-orders.status.update');
    Route::post('job-orders/{jobOrder}/assign', JobOrderAssignController::class)->name('job-orders.assign');
    Route::get('job-orders/{jobOrder}/history', [JobOrderHistoryController::class, 'index'])->name('job-orders.history.index');
});
