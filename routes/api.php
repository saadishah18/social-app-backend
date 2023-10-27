<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SettingsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::post('login', [AuthController::class, 'login']);
Route::post('social-login', [AuthController::class, 'social']);
Route::post('register', [AuthController::class, 'register']);
Route::post('phone/send-otp', [AuthController::class, 'sendPhoneOTP']);
Route::post('email/send-email-otp', [AuthController::class, 'sendEmailOTP']);
Route::post('phone/verify', [AuthController::class, 'verifyPhone']);
Route::post('email/verify-email-otp', [AuthController::class, 'verifyEmailOTP']);
Route::post('forgot-password/send-opt', [AuthController::class, 'sendForgotPasswordOTP']);
Route::post('forgot-password/resend', [AuthController::class, 'resendForgotPasswordOTP']);
Route::post('forgot-password/reset-password', [AuthController::class, 'resetPassword']);
Route::post('forgot-password/update-password', [AuthController::class, 'updatePassword']);

Route::get('terms-conditions',[SettingsController::class,'termsAndConditions']);
Route::get('privacy-policy',[SettingsController::class,'privacyPolicy']);
Route::get('about-us',[SettingsController::class,'aboutUs']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('update-device-token', [AuthController::class, 'updateDeviceToken']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user/profile', [ProfileController::class, 'profile']);
    Route::post('user/change-password', [ProfileController::class, 'changePassword']);
    Route::post('upload-logo',[ProfileController::class,'uploadLogo']);
//    Route::patch('profile', [ProfileController::class, 'update']);

    Route::get('posts',[PostController::class,'index']);
    Route::post('posts/store',[PostController::class,'store']);
    Route::post('posts/update',[PostController::class,'update']);
    Route::get('my-employees',[UserController::class,'getAllUsersForMobile']);
    Route::post('users/change-status',[UserController::class,'changeStatus']);
    Route::post('users/admin-users',[UserController::class,'adminUsers']);
    Route::post('users/delete-account',[UserController::class,'deleteAccount']);
    Route::apiResource('users',UserController::class);

    Route::get('admin-dashboard',[DashboardController::class,'adminDashboard']);

    Route::post('update-terms-policy',[SettingsController::class,'updatePolicyAndTerms']);

    Route::get('share-post',[PostController::class,'sharePost']);

});
