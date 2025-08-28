<?php

namespace App\Http\Controllers;
use App\Models\Event; use Illuminate\Http\Request;


class EventController extends Controller {
    public function index(){
        return Event::with('media')->orderBy('date','desc')->paginate(20);
    }


    public function store(Request $r){

        $data = $r->validate([
            'title'=>'required',
            'description'=>'nullable',
            'location'=>'nullable',
            'date'=>'required|date',
            'media' => 'array',
        ]);
        $event= Event::create($data);

        if ($r->has('media')) {
            foreach ($r->media as $index => $m) {
                if ($m['media_type'] === 'image') {

                    $file = $r->file("media.$index.file");
                    $path = $file->store('events', 'public');
                    $event->media()->create([
                        'media_type' => 'image',
                        'media_url' => $path
                    ]);

                } elseif ($m['media_type'] === 'video' && !empty($m['url'])) {
                    $event->media()->create([
                        'media_type' => 'video',
                        'media_url' => $m['url']
                    ]);
                }
            }
        }



        return response()->json($event);
    }
    public function show(Event $event)
    {
        $event->load('media');
        return response()->json($event);
    }

    public function update(Request $r, Event $event) {
        $data = $r->validate([
            'title' => 'sometimes|required',
            'description' => 'nullable',
            'location' => 'nullable',
            'date' => 'sometimes|date',
            'media' => 'array',
        ]);

        $event->update($data);

        if ($r->has('media')) {
            $existingIds = [];

            foreach ($r->media as $index => $m) {
                if (!empty($m['id'])) {
                    // Bestaande media bijwerken
                    $media = $event->media()->find($m['id']);
                    if ($media) {
                        if ($m['media_type'] === 'video') {
                            $media->update([
                                'media_type' => 'video',
                                'media_url' => $m['url']
                            ]);
                        }
                        if ($m['media_type'] === 'image') {
                            if ($r->file("media.$index.file")) {
                                $file = $r->file("media.$index.file");
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
                        $file = $r->file("media.$index.file");
                        if($file) {
                            $path = $file->store('events', 'public');
                            $media = $event->media()->create([
                                'media_type' => 'image',
                                'media_url' => $path
                            ]);
                        }

                        $existingIds[] = $media->id;

                    } elseif ($m['media_type'] === 'video' && !empty($m['url'])) {
                        $media = $event->media()->create([
                            'media_type' => 'video',
                            'media_url' => $m['url']
                        ]);
                        $existingIds[] = $media->id;
                    }
                }
            }
            // Verwijder alleen media die NIET in de nieuwe lijst zitten
            $event->media()->whereNotIn('id', $existingIds)->delete();
        }

        return $event->load('media');
    }



    public function destroy(Event $event){ $event->delete(); return response()->noContent(); }
}
