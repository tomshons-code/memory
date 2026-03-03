<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\EmailVerificationController;
use App\Http\Controllers\Admin\NewPasswordController;
use App\Http\Controllers\Admin\SettingController;

Route::post('login',       [AuthController::class, 'login']);

Route::post('password/email',   [NewPasswordController::class, 'forgotPassword']);
Route::post('password/reset',   [NewPasswordController::class, 'reset']);

Route::middleware('auth:admins')->group(function() {
    Route::post('logout',   [AuthController::class, 'logout']);

    Route::resource('courses', CourseController::class)->only([
        'index', 'store', 'show', 'update', 'destroy'
    ]);

    Route::resource('settings', SettingController::class)->only([
        'index', 'show', 'update'
    ]);

    Route::resource('users', UserController::class)->only([
        'index', 'store', 'show', 'update', 'destroy'
    ]);
    Route::get('profile',               [UserController::class, 'profile']);
    Route::put('update-password',       [UserController::class, 'updatePassword']);
    Route::post('upload-image',         [CourseController::class, 'uploadimage']);
    Route::get('course/{course}/clone', [CourseController::class, 'clone']);
});
