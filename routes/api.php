<?php

// TODO: 認証周りをlaravelのデフォルトで行い、メール確認機能をつける

use App\Http\Controllers\AuthBasicController;
use App\Http\Controllers\AuthBearerController;
use App\Http\Controllers\AuthEmailVerifyController;
use App\Http\Controllers\AuthLogoutController;
use App\Http\Controllers\AuthTestController;
use App\Http\Controllers\EmailVerifyIdHashController;
use App\Http\Controllers\InvitationCreateController;
use App\Http\Controllers\InvitationDeleteController;
use App\Http\Controllers\InvitationReadController;
use App\Http\Controllers\InvitationUpdateController;
use App\Http\Controllers\TaskCreateController;
use App\Http\Controllers\TaskDeleteController;
use App\Http\Controllers\TaskReadController;
use App\Http\Controllers\TaskSortController;
use App\Http\Controllers\UserCreateController;
use App\Http\Controllers\UserDeleteController;
use App\Http\Controllers\WorkCreateController;
use App\Http\Controllers\WorkDeleteController;
use App\Http\Controllers\WorkReadMonthController;
use App\Http\Controllers\WorkResetController;
use Illuminate\Support\Facades\Route;

// テストルート
Route::get('/test', fn () => ['message' => 'this is test 1']); // 🗒️

// --- Cookie 認証が必要なルートは web ミドルウェア必須 ---
Route::middleware(['web'])->group(function () {

    // メール認証
    Route::middleware(['signed'])->group(function () {
        Route::get('/email/verify/{id}/{hash}', [EmailVerifyIdHashController::class, 'index'])->name('verification.verify');
    });

    // user
    Route::post('/user/auth/basic', [AuthBasicController::class, 'index']);
    Route::get('/user/auth/test', [AuthTestController::class, 'index']); // 🗒️
    Route::post('/user/auth/logout', [AuthLogoutController::class, 'index']);
    Route::post('/user/create', [UserCreateController::class, 'index']);

    Route::middleware(['auth:sanctum'])->group(function () {

        // user
        Route::get('/user/auth/bearer', [AuthBearerController::class, 'index']);
        Route::post('/user/auth/email/verify', [AuthEmailVerifyController::class, 'index']);
        Route::delete('/user/delete', [UserDeleteController::class, 'index']);

        // task
        Route::get('/task/read', [TaskReadController::class, 'index']);
        Route::post('/task/create', [TaskCreateController::class, 'index']);
        Route::post('/task/sort', [TaskSortController::class, 'index']);
        Route::delete('/task/delete', [TaskDeleteController::class, 'index']);

        // work
        Route::get('/work/read/month', [WorkReadMonthController::class, 'index']);
        Route::post('/work/create', [WorkCreateController::class, 'index']);
        Route::delete('/work/delete', [WorkDeleteController::class, 'index']);
        Route::delete('/work/reset', [WorkResetController::class, 'index']);

        // invitation
        Route::get('/invitation/read', [InvitationReadController::class, 'index']);
        Route::post('/invitation/create', [InvitationCreateController::class, 'index']);
        Route::put('/invitation/update', [InvitationUpdateController::class, 'index']);
        Route::delete('/invitation/delete', [InvitationDeleteController::class, 'index']);
    });
});
