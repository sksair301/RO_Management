<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\RoFormController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountsController;


Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::middleware(['jwt'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['jwt'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'adminDashboard']);

    // User CRUD
    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::patch('/user/edit/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // Roles CRUD
    Route::get('/roles', [RolesController::class, 'index']);
    Route::post('/roles', [RolesController::class, 'store']);
    Route::get('/roles/{id}', [RolesController::class, 'show']);
    Route::patch('/roles/edit/{id}', [RolesController::class, 'update']);
    Route::delete('/roles/{id}', [RolesController::class, 'destroy']);

    // Departments CRUD
    Route::get('/departments', [DepartmentsController::class, 'index']);
    Route::post('/departments', [DepartmentsController::class, 'store']);
    Route::get('/departments/{id}', [DepartmentsController::class, 'show']);
    Route::patch('/departments/{id}', [DepartmentsController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentsController::class, 'destroy']);

    // Permissions CRUD
    Route::apiResource('/permissions', PermissionController::class);

});

Route::middleware(['jwt'])->group(function () {

    // Vendor CRUD
    Route::get('/vendor', [VendorController::class, 'index']);
    Route::post('/vendor', [VendorController::class, 'store']);
    Route::get('/vendor/{id}', [VendorController::class, 'show']);
    Route::patch('/vendor/{id}', [VendorController::class, 'update']);
    Route::delete('/vendor/{id}', [VendorController::class, 'destroy']);

    // RO Form CRUD & Approval Status
    Route::get('/roForm', [RoFormController::class, 'index']);
    Route::post('/roForm', [RoFormController::class, 'store']);
    Route::get('/roForm/{id}', [RoFormController::class, 'show']);
    Route::patch('/roForm/{id}', [RoFormController::class, 'update']);
    Route::delete('/roForm/{id}', [RoFormController::class, 'destroy']);

    Route::patch('/roForm/{id}/approve', [RoFormController::class, 'approve']);
    Route::patch('/roForm/{id}/reject', [RoFormController::class, 'reject']);
    Route::patch('/roForm/{id}/cancel', [RoFormController::class, 'cancel']);
});


Route::middleware(['jwt'])->prefix('accounts')->group(function () {
    Route::get('/', [AccountsController::class, 'index']);
    Route::post('/', [AccountsController::class, 'store']);
    Route::get('/{id}', [AccountsController::class, 'show']);
    Route::patch('/{id}', [AccountsController::class, 'update']);
    Route::delete('/{id}', [AccountsController::class, 'destroy']);
});
