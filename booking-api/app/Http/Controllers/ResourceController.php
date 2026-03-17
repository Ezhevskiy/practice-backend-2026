<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::where('is_active', true);

        // Фильтры
        if ($request->filled('capacity_min')) {
            $query->where('capacity', '>=', $request->capacity_min);
        }

        if ($request->filled('projector')) {
            $query->where('has_projector', $request->projector);
        }

        if ($request->filled('whiteboard')) {
            $query->where('has_whiteboard', $request->whiteboard);
        }

        $resources = $query->paginate(10);

        return response()->json($resources);
    }

    public function show(Resource $resource)
    {
        $resource->loadCount('reviews');
        return response()->json($resource);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:100',
            'description'           => 'nullable|string',
            'capacity'              => 'required|integer|min:1',
            'floor'                 => 'nullable|integer',
            'has_projector'         => 'boolean',
            'has_whiteboard'        => 'boolean',
            'has_video_conference'  => 'boolean',
            'has_monitor'           => 'boolean',
            'has_speakers'          => 'boolean',
            'price_per_hour'        => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $resource = Resource::create($request->all());

        return response()->json($resource, 201);
    }

    public function update(Request $request, Resource $resource)
    {
        $resource->update($request->all());
        return response()->json($resource);
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return response()->json(['message' => 'Ресурс удалён']);
    }

    public function schedule(Request $request, Resource $resource)
    {
        $validator = Validator::make($request->all(), [
            'date'   => 'required|date',
            'period' => 'nullable|in:day,week',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $date = Carbon::parse($request->date);
        $period = $request->period ?? 'day';

        $start = $date->copy()->startOfDay();
        $end = $period === 'week' ? $date->copy()->endOfWeek() : $date->copy()->endOfDay();

        $bookings = Booking::where('resource_id', $resource->id)
            ->whereBetween('starts_at', [$start, $end])
            ->orderBy('starts_at')
            ->get();

        return response()->json([
            'resource' => $resource,
            'period'   => $period,
            'date'     => $date->toDateString(),
            'bookings' => $bookings,
        ]);
    }

    public function available(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date'        => 'required|date',
            'time_from'   => 'required|date_format:H:i',
            'time_to'     => 'required|date_format:H:i|after:time_from',
            'capacity'    => 'nullable|integer|min:1',
            'projector'   => 'nullable|boolean',
            'whiteboard'  => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $date = $request->date;
        $start = Carbon::parse("$date {$request->time_from}:00");
        $end   = Carbon::parse("$date {$request->time_to}:00");

        $query = Resource::where('is_active', true);

        if ($request->capacity) {
            $query->where('capacity', '>=', $request->capacity);
        }

        if ($request->projector) {
            $query->where('has_projector', true);
        }

        if ($request->whiteboard) {
            $query->where('has_whiteboard', true);
        }

        $resources = $query->get();

        $free = $resources->filter(function ($resource) use ($start, $end) {
            return !Booking::where('resource_id', $resource->id)
                ->where(function ($q) use ($start, $end) {
                    $q->where(function ($sub) use ($start, $end) {
                        $sub->where('starts_at', '<', $end)
                            ->where('ends_at', '>', $start);
                    });
                })
                ->exists();
        })->values();

        return response()->json($free);
    }
}