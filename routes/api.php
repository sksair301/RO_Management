<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\DepartmentsController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login',[AuthController::class,'login']);



Route::middleware(['jwt'])->prefix('admin')->group(function () {
    Route::get('/user',[UserController::class,'index']);
    Route::post('/user',[UserController::class,'store']);
    Route::get('/user/{id}',[UserController::class,'show']);
    Route::patch('/user/{id}',[UserController::class,'update']);

    Route::get('/roles',[RolesController::class,'index']);
    Route::post('/roles',[RolesController::class,'store']);

    Route::get('/departments',[DepartmentsController::class,'index']);
    Route::post('/departments', [DepartmentsController::class,'store']);

    Route::apiResource('/permissions',PermissionController::class);

    Route::post('roles/{roleId}/permissions', [RolePermissionController::class, 'assignPermission']);
    Route::delete('roles/{roleId}/permissions', [RolePermissionController::class, 'removePermission']);

});
