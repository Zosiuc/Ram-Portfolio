<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use App\Models\UserMeta;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function index(UserMeta $userMeta)
    {
        return response()->json($userMeta->socialMedia);
    }

    public function create()
    {
        return response()->json(['message' => 'Social media create form (frontend)']);
    }

    public function store(Request $request, UserMeta $userMeta)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'url' => 'required|url',
        ]);

        $socialMedia = $userMeta->socialMedia()->create($validated);

        return response()->json($socialMedia, 201);
    }

    public function show(SocialMedia $socialMedia)
    {
        return response()->json($socialMedia);
    }

    public function update(Request $request, SocialMedia $socialMedia)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'url' => 'required|url',
        ]);

        $socialMedia->update($validated);

        return response()->json($socialMedia);
    }

    public function destroy(SocialMedia $socialMedia)
    {
        $socialMedia->delete();
        return response()->json(null, 204);
    }
}
