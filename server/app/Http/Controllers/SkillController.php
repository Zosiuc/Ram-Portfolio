<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\UserMeta;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index(UserMeta $userMeta)
    {
        return response()->json($userMeta->skills);
    }

    public function create()
    {
        return response()->json(['message' => 'Skill create form (frontend)']);
    }

    public function store(Request $request, UserMeta $userMeta)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $skill = $userMeta->skills()->create($validated);

        return response()->json($skill, 201);
    }

    public function show(Skill $skill)
    {
        return response()->json($skill);
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $skill->update($validated);

        return response()->json($skill);
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();
        return response()->json(null, 204);
    }
}
