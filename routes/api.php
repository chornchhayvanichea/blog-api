<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

#Route::get('/debug-post/{post}', function (Post $post) {
#    return response()->json([
#        'post_exists' => true,
#        'post_id' => $post->id,
#        'post_user_id' => $post->user_id,
#        'post' => $post
#    ]);
#});
#Route::middleware('auth:api')->get('/debug-auth', function () {
#    $user = auth('api')->user();
#
#    if (!$user) {
#        return response()->json(['error' => 'No user found'], 401);
#    }
#
#    return response()->json([
#        'auth_working' => true,
#        'user_id' => $user->id,
#        'user_email' => $user->email,
#        'user_role' => $user->role,
#        'user' => $user
#    ]);
#});
#
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class,'login']);
    Route::post('/signup', [AuthController::class,'signup']);
    Route::post('/forgot-password', [AuthController::class,'forgotPassword']);
    Route::post('/reset-password', [AuthController::class,'resetPassword']);

    Route::middleware(['auth:api','banned'])->group(function () {
        Route::post('/logout', [AuthController::class,'logout']);
        Route::post('/refresh-token', [AuthController::class,'refreshToken']);
        Route::post('/change-password', [AuthController::class,'changePassword']);
    });
});

Route::middleware(['auth:api','banned'])->group(function () {
    Route::prefix('posts')->group(function () {

        Route::post('/', [PostController::class, 'store']);
        Route::get('/', [PostController::class, 'index']);
        Route::match(['post','put','patch'], '/{post}', [PostController::class,'update']);
        Route::get('/{post}', [PostController::class, 'show']);
        Route::delete('/{post}', [PostController::class, 'softDelete']);

        Route::prefix('{post}/comments')->group(function () {
            Route::get('/', [CommentController::class,'index']);
            //            Route::get('/{comment}', [CommentController::class,'show']);
            Route::post('/', [CommentController::class,'store']);
            Route::patch('/{comment}', [CommentController::class,'edit']);
            Route::delete('/{comment}', [CommentController::class,'delete']);
        });
    });

    Route::post('/reports', [ReportController::class,'storeReport']);
});

Route::middleware(['auth:api','admin','banned'])->group(function () {
    Route::patch('/users/{user}/ban', [UserManagementController::class,'toggleBan']);
    Route::patch('/posts/{post}/restore', [PostController::class,'restore']);

    Route::get('/reports', [ReportController::class,'index']);
});
