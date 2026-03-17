<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Ресурсы — просмотр всем авторизованным
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/{resource}', [ResourceController::class, 'show']);

    // Изменение ресурсов — только админ
    Route::middleware('role:admin')->group(function () {
        Route::post('/resources', [ResourceController::class, 'store']);
        Route::put('/resources/{resource}', [ResourceController::class, 'update']);
        Route::delete('/resources/{resource}', [ResourceController::class, 'destroy']);
    });

    // Расширенные функции Этапа 4
    Route::get('/resources/available', [ResourceController::class, 'available']);
    Route::get('/resources/{resource}/schedule', [ResourceController::class, 'schedule']);

    // Бронирования — доступны всем авторизованным
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/{booking}', [BookingController::class, 'show']);
        Route::delete('/{booking}', [BookingController::class, 'destroy']);
    });

    // Отзывы
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/resources/{resource}/reviews', [ReviewController::class, 'indexByResource']);
});