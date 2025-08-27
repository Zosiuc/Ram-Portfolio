<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\UserMeta;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(UserMeta $userMeta)
    {
        return response()->json($userMeta->experience);
    }

    public function create()
    {
        return response()->json(['message' => 'Experience create form (frontend)']);
    }

    public function store(Request $request, UserMeta $userMeta)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'company_name' => 'nullable|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $experience = $userMeta->experience()->create($validated);

        return response()->json($experience, 201);
    }

    public function show(Experience $experience)
    {
        return response()->json($experience);
    }

    public function update(Request $request, Experience $experience)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'company_name' => 'nullable|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $experience->update($validated);

        return response()->json($experience);
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();
        return response()->json(null, 204);
    }
}
