<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', auth('api')->id())
            ->with('resource')
            ->orderBy('starts_at', 'desc')
            ->paginate(10);

        return response()->json($bookings);
    }

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

        return response()->json($booking, 201);
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth('api')->id() && auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Доступ запрещён'], 403);
        }

        $booking->load('resource');
        return response()->json($booking);
    }

    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== auth('api')->id() && auth('api')->user()->role !== 'admin') {
            return response()->json(['error' => 'Доступ запрещён'], 403);
        }

        $booking->delete();
        return response()->json(['message' => 'Бронирование отменено']);
    }
}