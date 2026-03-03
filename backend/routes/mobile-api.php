<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\CourseController;
use App\Http\Controllers\Mobile\AnswerController;
use App\Http\Controllers\Mobile\EmailVerificationController;
use App\Http\Controllers\Mobile\NewPasswordController;

Route::post('register',    [AuthController::class, 'register']);
Route::post('login',       [AuthController::class, 'login']);

Route::post('forgot-password',  [NewPasswordController::class, 'forgotPassword']);
Route::post('reset-password',   [NewPasswordController::class, 'reset']);


Route::post('email/verification',       [EmailVerificationController::class, 'sendVerificationEmail'])->name('verification.send')->middleware('auth:mobile');
Route::get('verify-email/{id}/{hash}',  [EmailVerificationController::class, 'verify']);

Route::middleware('auth:mobile', 'verified')->group(function() {
    Route::get('profile',           [AuthController::class, 'profile']);
    Route::get('about',             [AuthController::class, 'about']);
    Route::put('profile',           [AuthController::class, 'profileSave']);
    Route::put('update_password',   [AuthController::class, 'updatePassword']);
    Route::post('logout',           [AuthController::class, 'logout']);

    Route::resource('courses',  CourseController::class)->only([
        'index', 'show'
    ]);
    Route::get('courses/{course}/ranking', [CourseController::class, 'ranking']);
    Route::post('courses-access/{course}', [CourseController::class, 'access']);
    Route::resource('answer',   AnswerController::class)->only([
        'update'
    ]);
});
