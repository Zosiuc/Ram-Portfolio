<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\UserMeta;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index(UserMeta $userMeta)
    {
        return response()->json($userMeta->education);
    }

    public function create()
    {
        return response()->json(['message' => 'Education create form (frontend)']);
    }

    public function store(Request $request, UserMeta $userMeta)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $education = $userMeta->education()->create($validated);

        return response()->json($education, 201);
    }

    public function show(Education $education)
    {
        return response()->json($education);
    }

    public function update(Request $request, Education $education)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $education->update($validated);

        return response()->json($education);
    }

    public function destroy(Education $education)
    {
        $education->delete();
        return response()->json(null, 204);
    }
}
