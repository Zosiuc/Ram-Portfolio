<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class SubjectController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Subject::all()
        ]);
    }

    public function show($id)
    {
        $subject = Subject::with('media')->findOrFail($id);
        return response()->json($subject);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'portfolio_item_id' => 'required|exists:portfolio_items,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'media' => 'array',
        ]);

        $subject = Subject::create($validated);

        if ($request->has('media')) {
            foreach ($request->media as $m) {
                if ($m['media_type'] === 'image' && isset($m['file'])) {
                    $path = $m['file']->store('subjects', 'public');
                    $subject->media()->create([
                        'media_type' => 'image',
                        'media_url' => $path
                    ]);
                } elseif ($m['media_type'] === 'video') {
                    $subject->media()->create([
                        'media_type' => 'video',
                        'media_url' => $m['url']
                    ]);
                }
            }
        }

        return response()->json($subject->load('media'), 201);
    }

    public function update(Request $request, Subject $subject)
    {

        $validated = $request->validate([
            'portfolio_item_id' => 'required|exists:portfolio_items,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'media' => 'array',
        ]);

        $subject->update($validated);

        // Eerst media verwijderen (later kan je dit slimmer maken)
        $subject->media()->delete();

        if ($request->has('media')) {
            foreach ($request->media as $index => $m) {
                if ($m['media_type'] === 'image') {
                    $file = $request->file("media.$index.file");
                    if ($file) {
                        $path = $file->store('subjects', 'public');
                        $subject->media()->create([
                            'media_type' => 'image',
                            'media_url' => $path
                        ]);
                    }
                } elseif ($m['media_type'] === 'video' && !empty($m['url'])) {
                    $subject->media()->create([
                        'media_type' => 'video',
                        'media_url' => $m['url']
                    ]);
                }
            }
        }

        return response()->json($subject->load('media'));
    }

    public function destroy(Subject $subject){ $subject->delete(); return response()->noContent(); }
}
