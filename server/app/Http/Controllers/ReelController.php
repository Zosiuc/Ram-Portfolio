<?php

namespace App\Http\Controllers;

use App\Models\Reel;
use App\Models\UserMeta;
use Illuminate\Http\Request;

class ReelController extends Controller
{
    public function index(UserMeta $userMeta)
    {
        return response()->json($userMeta->reels);
    }

    public function create()
    {
        return response()->json(['message' => 'Reel create form (frontend)']);
    }

    public function store(Request $request, UserMeta $userMeta)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'url' => 'required|url',
        ]);

        $reel = $userMeta->reels()->create($validated);

        return response()->json($reel, 201);
    }

    public function show(Reel $reel)
    {
        return response()->json($reel);
    }

    public function update(Request $request, Reel $reel)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'url' => 'required|url',
        ]);

        $reel->update($validated);

        return response()->json($reel);
    }

    public function destroy(Reel $reel)
    {
        $reel->delete();
        return response()->json(null, 204);
    }
}
