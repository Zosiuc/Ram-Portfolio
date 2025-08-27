<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Http\Request;

class EventMediaController extends Controller
{
    public function show(EventMedia $subjectMedia){ return $subjectMedia; }
    // 1. Media opslaan bij een subject
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'media_type' => 'required|in:image,video',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'url' => 'nullable|string|max:255'
        ]);

        $mediaUrl = null;

        if ($validated['media_type'] === 'image' && $request->hasFile('file')) {
            $mediaUrl = $request->file('file')->store('events', 'public');
        } elseif ($validated['media_type'] === 'video' && !empty($validated['url'])) {
            $mediaUrl = $validated['url'];
        }

        $media = $event->media()->create([
            'media_type' => $validated['media_type'],
            'media_url' => $mediaUrl,
        ]);

        return response()->json($media, 201);
    }

    // 2. Media updaten
    public function update(Request $request, EventMedia $media)
    {
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
    public function destroy(EventMedia $media)
    {
        $media->delete();
        return response()->json(['message' => 'Media deleted successfully']);
    }
}
