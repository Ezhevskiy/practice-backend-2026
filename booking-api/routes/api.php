<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\BookingController;

// Публичные маршруты (регистрация и логин)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Все маршруты ниже требуют авторизации (токен)
Route::middleware('auth:api')->group(function () {

    // Общие: кто я, выход
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Ресурсы: просмотр доступен всем авторизованным, создание/изменение — только админу
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/{resource}', [ResourceController::class, 'show']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/resources', [ResourceController::class, 'store']);
        Route::put('/resources/{resource}', [ResourceController::class, 'update']);
        Route::delete('/resources/{resource}', [ResourceController::class, 'destroy']);
    });

    // Бронирования: обычный пользователь видит только свои, админ — все
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);         // свои бронирования
        Route::post('/', [BookingController::class, 'store']);       // создать
        Route::get('/{booking}', [BookingController::class, 'show']); // просмотр конкретного
        Route::delete('/{booking}', [BookingController::class, 'destroy']); // отменить
    });
});