<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->user_id !== auth('api')->id()) {
            return response()->json(['error' => 'Это не ваша бронь'], 403);
        }

        if ($booking->ends_at->isFuture() || $booking->status === 'cancelled') {
            return response()->json(['error' => 'Бронирование ещё не завершено'], 403);
        }

        if ($booking->review) {
            return response()->json(['error' => 'Отзыв уже оставлен'], 409);
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'user_id'    => auth('api')->id(),
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return response()->json($review, 201);
    }

    public function indexByResource(Request $request, $resourceId)
    {
        $reviews = Review::whereHas('booking', function ($q) use ($resourceId) {
            $q->where('resource_id', $resourceId);
        })
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reviews);
    }
}