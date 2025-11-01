<?php

use App\Http\Controllers\AuthBasicController;
use App\Http\Controllers\AuthBearerController;
use App\Http\Controllers\AuthEmailVerifyController;
use App\Http\Controllers\AuthLogoutController;
use App\Http\Controllers\AuthPasswordForgotController;
use App\Http\Controllers\AuthPasswordResetController;
use App\Http\Controllers\AuthTestController;
use App\Http\Controllers\EmailVerifyIdHashController;
use App\Http\Controllers\InvitationCreateController;
use App\Http\Controllers\InvitationDeleteController;
use App\Http\Controllers\InvitationReadController;
use App\Http\Controllers\InvitationUpdateController;
use App\Http\Controllers\TaskCreateController;
use App\Http\Controllers\TaskDeleteController;
use App\Http\Controllers\TaskReadController;
use App\Http\Controllers\TaskRestoreController;
use App\Http\Controllers\TaskSortController;
use App\Http\Controllers\UserCreateController;
use App\Http\Controllers\UserDeleteController;
use App\Http\Controllers\WorkCreateController;
use App\Http\Controllers\WorkDeleteController;
use App\Http\Controllers\WorkReadMonthController;
use App\Http\Controllers\WorkResetController;
use Illuminate\Support\Facades\Route;

// テストルート
Route::get('/test', fn () => ['message' => 'this is test 1']);

// --- Cookie 認証が必要なルートは web ミドルウェア必須 ---
Route::middleware(['web'])->group(function () {

    // user（認証前）
    Route::post('/user/create', [UserCreateController::class, 'index']);
    Route::get('/user/auth/test', [AuthTestController::class, 'index']);
    Route::post('/user/auth/basic', [AuthBasicController::class, 'index']);
    Route::post('/user/auth/password/forgot', [AuthPasswordForgotController::class, 'index']);
    Route::post('/user/auth/password/reset', [AuthPasswordResetController::class, 'index']); // TODO: test
    Route::post('/user/auth/logout', [AuthLogoutController::class, 'index']); // TODO: test
    Route::get('/user/auth/email/verify/{id}/{hash}', [EmailVerifyIdHashController::class, 'index']); // TODO: test

    Route::middleware(['auth:sanctum'])->group(function () {

        // user（認証後）
        Route::get('/user/auth/bearer', [AuthBearerController::class, 'index']);
        Route::post('/user/auth/email/verify/send', [AuthEmailVerifyController::class, 'index']);
        Route::delete('/user/delete', [UserDeleteController::class, 'index']);

        // task
        Route::get('/task/read', [TaskReadController::class, 'index']); // TODO: test
        Route::post('/task/create', [TaskCreateController::class, 'index']);
        Route::post('/task/sort', [TaskSortController::class, 'index']);
        Route::delete('/task/delete', [TaskDeleteController::class, 'index']);
        Route::post('/task/restore', [TaskRestoreController::class, 'index']);

        // work
        Route::get('/work/read/month', [WorkReadMonthController::class, 'index']); // TODO: test
        Route::post('/work/create', [WorkCreateController::class, 'index']);
        Route::delete('/work/delete', [WorkDeleteController::class, 'index']); // TODO: test
        Route::delete('/work/reset', [WorkResetController::class, 'index']); // TODO: test

        // invitation
        Route::get('/invitation/read', [InvitationReadController::class, 'index']); // TODO: test
        Route::post('/invitation/create', [InvitationCreateController::class, 'index']); // TODO: test
        Route::put('/invitation/update', [InvitationUpdateController::class, 'index']); // TODO: test
        Route::delete('/invitation/delete', [InvitationDeleteController::class, 'index']); // TODO: test
    });
});
