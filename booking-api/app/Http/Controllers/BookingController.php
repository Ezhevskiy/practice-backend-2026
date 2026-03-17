<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    /**
     * Список своих бронирований (для текущего пользователя)
     * Поддерживает пагинацию, сортировку и базовую фильтрацию
     */
    public function index(Request $request)
    {
        $query = Booking::where('user_id', auth('api')->id())
            ->with('resource:id,name,capacity')
            ->orderBy($request->input('sort_by', 'starts_at'), $request->input('sort_dir', 'desc'));

        // Фильтры (опциональные)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('starts_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('ends_at', '<=', $request->to_date);
        }

        $bookings = $query->paginate(10);

        return response()->json($bookings);
    }

    /**
     * Создать новое бронирование
     * Проверяет пересечение интервалов
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required|exists:resources,id',
            'starts_at'   => 'required|date|after:now',
            'ends_at'     => 'required|date|after:starts_at',
            'notes'       => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = new Booking([
            'user_id'     => auth('api')->id(),
            'resource_id' => $request->resource_id,
            'starts_at'   => $request->starts_at,
            'ends_at'     => $request->ends_at,
            'notes'       => $request->notes ?? null,
            'status'      => 'confirmed',
        ]);

        if ($booking->hasConflict()) {
            return response()->json(['error' => 'Выбранное время уже занято'], 409);
        }

        $booking->save();

        $booking->load('resource');

        return response()->json($booking, 201);
    }

    /**
     * Просмотр конкретного бронирования
     * Доступ: только владелец или админ
     */
    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth('api')->id() && auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Доступ запрещён'], 403);
        }

        $booking->load('resource');

        return response()->json($booking);
    }

    /**
     * Отмена бронирования
     * Обычный пользователь может отменить только до начала
     * Админ может отменить любое
     */
    public function destroy(Booking $booking)
    {
        $user = auth('api')->user();

        if ($booking->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Доступ запрещён'], 403);
        }

        // Обычный пользователь может отменить только до начала
        if ($user->role !== 'admin' && Carbon::parse($booking->starts_at)->isPast()) {
            return response()->json(['error' => 'Нельзя отменить бронирование после начала'], 403);
        }

        $booking->update(['status' => 'cancelled']);
        // $booking->delete(); // или мягкое удаление, если хочешь

        return response()->json(['message' => 'Бронирование отменено']);
    }

    /**
     * (Опционально) Для админа: список ВСЕХ бронирований
     */
    public function adminIndex(Request $request)
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Доступ запрещён'], 403);
        }

        $bookings = Booking::with(['user:id,name', 'resource:id,name'])
            ->orderBy('starts_at', 'desc')
            ->paginate(15);

        return response()->json($bookings);
    }
}