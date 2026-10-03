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

// Auth (Public)
Route::post('auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('auth.login');

// Protected Routes
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');

    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');

    // Customers
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('customers/{customer}/vehicles', [CustomerController::class, 'vehicles'])->name('customers.vehicles');
    Route::apiResource('customers', CustomerController::class)->except(['destroy']);

    // Vehicles
    Route::get('vehicles/search', [VehicleController::class, 'search'])->name('vehicles.search');
    Route::apiResource('vehicles', VehicleController::class)->except(['index', 'destroy']);

    // Workshops
    Route::apiResource('workshops', WorkshopController::class);

    // Job Orders
    Route::apiResource('job-orders', JobOrderController::class);

    // Job Order Media
    Route::get('job-orders/{jobOrder}/media', [JobOrderMediaController::class, 'index'])->name('job-orders.media.index');
    Route::post('job-orders/{jobOrder}/media', [JobOrderMediaController::class, 'store'])->name('job-orders.media.store');
    Route::delete('job-orders/{jobOrder}/media/{media}', [JobOrderMediaController::class, 'destroy'])->name('job-orders.media.destroy');
    Route::delete('job-order-media/{media}', [JobOrderMediaController::class, 'destroy']);

    // Job Order Workflow
    Route::patch('job-orders/{jobOrder}/assign-technician', JobOrderAssignController::class)->name('job-orders.assign-technician');
    Route::post('job-orders/{jobOrder}/assign', JobOrderAssignController::class)->name('job-orders.assign');
    Route::patch('job-orders/{jobOrder}/status', [JobOrderStatusController::class, 'update'])->name('job-orders.status.update');
    Route::post('job-orders/{jobOrder}/status-correction', [JobOrderStatusController::class, 'correct'])->name('job-orders.status.correction');
    Route::get('job-orders/{jobOrder}/status-history', [JobOrderHistoryController::class, 'index'])->name('job-orders.status-history.index');
    Route::get('job-orders/{jobOrder}/history', [JobOrderHistoryController::class, 'index'])->name('job-orders.history.index');
});

