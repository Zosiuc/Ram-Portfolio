<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectMedia;
use Illuminate\Http\Request;

class SubjectMediaController extends Controller
{
    public function show(SubjectMedia $subjectMedia){ return $subjectMedia; }
    // 1. Media opslaan bij een subject
    public function store(Request $request, Subject $subject)
    {
        \Log::info($request->all());
        $validated = $request->validate([
            'media_type' => 'required|in:image,video',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'url' => 'nullable|string|max:255'
        ]);

        $mediaUrl = null;

        if ($validated['media_type'] === 'image' && $request->hasFile('file')) {
            $mediaUrl = $request->file('file')->store('subjects', 'public');
        } elseif ($validated['media_type'] === 'video' && !empty($validated['url'])) {
            $mediaUrl = $validated['url'];
        }

        $media = $subject->media()->create([
            'media_type' => $validated['media_type'],
            'media_url' => $mediaUrl,
        ]);

        return response()->json($media, 201);
    }

    // 2. Media updaten
    public function update(Request $request, SubjectMedia $media)
    {
        \Log::info($request->all());
        $validated = $request->validate([
            'media_type' => 'required|in:image,video',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'url' => 'nullable|string|max:255'
        ]);

        $mediaUrl = $media->media_url;

        if ($validated['media_type'] === 'image' && $request->hasFile('file')) {
            $mediaUrl = $request->file('file')->store('subjects', 'public');
        } elseif ($validated['media_type'] === 'video' && !empty($validated['url'])) {
            $mediaUrl = $validated['url'];
        }

        $media->update([
            'media_type' => $validated['media_type'],
            'media_url' => $mediaUrl,
        ]);

        return response()->json($media);
    }

    // 3. Media verwijderen
    public function destroy(SubjectMedia $media)
    {
        $media->delete();
        return response()->json(['message' => 'Media deleted successfully']);
    }
}
