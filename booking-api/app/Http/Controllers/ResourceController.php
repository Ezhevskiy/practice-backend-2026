<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::where('is_active', true)
            ->select('id', 'name', 'capacity', 'floor', 'has_projector', 'has_whiteboard')
            ->paginate(10);

        return response()->json($resources);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'capacity'    => 'required|integer|min:1|max:100',
            'floor'       => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $resource = Resource::create($request->all());

        return response()->json($resource, 201);
    }

    public function show(Resource $resource)
    {
        return response()->json($resource);
    }

    public function update(Request $request, Resource $resource)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:100',
            'capacity' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $resource->update($request->all());
        return response()->json($resource);
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return response()->json(['message' => 'Ресурс успешно удалён']);
    }
}