<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;


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
        \Log::info($request->all());
        $validated = $request->validate([
            'portfolio_item_id' => 'required|exists:portfolio_items,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'media' => 'array',
        ]);

        $subject = Subject::create($validated);

        if ($request->has('media')) {
            foreach ($request->media as $index => $m) {
                if ($m['media_type'] === 'image') {

                    $file = $request->file("media.$index.file");
                    $path = $file->store('subjects', 'public');
                    $subject->media()->create([
                        'media_type' => 'image',
                        'media_url' => $path
                    ]);

                } elseif ($m['media_type'] === 'video' && !empty($m['url'])) {
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
        \Log::info($request->all());

        $validated = $request->validate([
            'portfolio_item_id' => 'required|exists:portfolio_items,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'media' => 'array',
        ]);

        $subject->update($validated);

        if ($request->has('media')) {
            $existingIds = [];

            foreach ($request->media as $index => $m) {
                if (!empty($m['id'])) {
                    // Bestaande media bijwerken
                    $media = $subject->media()->find($m['id']);
                    if ($media) {
                        if ($m['media_type'] === 'video') {
                            $media->update([
                                'media_type' => 'video',
                                'media_url' => $m['url']
                            ]);
                        }
                        if ($m['media_type'] === 'image') {
                            if ($request->file("media.$index.file")) {
                                $file = $request->file("media.$index.file");
                                $path = $file->store('subjects', 'public');
                            } else if ($m['url']) $path = $m['url'];

                            $media->update([
                                'media_type' => 'image',
                                'media_url' => $path
                            ]);


                        }
                        $existingIds[] = $m['id'];
                    }


                } else {
                    // Nieuwe media toevoegen
                    if ($m['media_type'] === 'image') {
                        $file = $request->file("media.$index.file");
                        if($file) {
                            $path = $file->store('subjects', 'public');
                            $media = $subject->media()->create([
                                'media_type' => 'image',
                                'media_url' => $path
                            ]);
                        }

                        $existingIds[] = $media->id;

                    } elseif ($m['media_type'] === 'video' && !empty($m['url'])) {
                        $media = $subject->media()->create([
                            'media_type' => 'video',
                            'media_url' => $m['url']
                        ]);
                        $existingIds[] = $media->id;
                    }
                }
            }
            // Verwijder alleen media die NIET in de nieuwe lijst zitten
            $subject->media()->whereNotIn('id', $existingIds)->delete();
        }

        return response()->json($subject->load('media'));
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->noContent();
    }
}
