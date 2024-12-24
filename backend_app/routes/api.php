<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\Article\AuthorController;
use App\Http\Controllers\Article\CategoryController;
use App\Http\Controllers\Article\SourceController;
use App\Http\Controllers\UserPreference\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// User authentication routes
Route::group(['prefix' => 'auth', 'middleware' => ['throttle:api']], function() {
    // public routes - register and login
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    // public routes - reset password
    Route::post('/forgot-password',  [ResetPasswordController::class, 'forgotPassword'])->name('auth.forgotPassword');
    Route::post('/password-code-check', [ResetPasswordController::class, 'passwordCodeCheck'])->name('auth.passwordCodeCheck');
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('auth.resetPassword');
    // authenticated routes
    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('auth.refreshToken');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logoutAll');
        Route::get('/user', function (Request $request) {
            return $request->user();
        })->name('user.profileInfo');
    });
});

// Article management routes
Route::group(['middleware' => ['auth:sanctum', 'throttle:api']], function() {
    Route::apiResource('articles', ArticleController::class)->only('index', 'show');
    // extra routes
    Route::apiResource('authors', AuthorController::class)->only('show');
    Route::apiResource('categories', CategoryController::class)->only('show');
    Route::apiResource('sources', SourceController::class)->only('show');
});

// User Preference routes
Route::group(['middleware' => ['auth:sanctum', 'throttle:api']], function() {
    Route::apiResource('user-preference', UserPreferenceController::class)->only('index', 'store', 'destroy');
    Route::get('/user-preference/show-feed', [UserPreferenceController::class, 'showFeed'])->name('user-preference.showFeed');
});
