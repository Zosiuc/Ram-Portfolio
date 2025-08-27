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
            foreach ($r->media as $m) {
                if ($m['media_type'] === 'image' && isset($m['file'])) {
                    $path = $m['file']->store('subjects', 'public');
                    $event->media()->create([
                        'media_type' => 'image',
                        'media_url' => $path
                    ]);
                } elseif ($m['media_type'] === 'video') {
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
        return $event->load('media');
    }


    public function update(Request $r, Event $event){
        $data = $r->validate([
            'title'=>'sometimes|required',
            'description'=>'nullable',
            'location'=>'nullable',
            'date'=>'sometimes|date',
            'media' => 'array',
        ]);
        $event->update($data);

        // Eerst media verwijderen (later kan je dit slimmer maken)
        $event->media()->delete();

        if ($r->has('media')) {
            foreach ($r->media as $index => $m) {
                if ($m['media_type'] === 'image') {
                    $file = $r->file("media.$index.file");
                    if ($file) {
                        $path = $file->store('subjects', 'public');
                        $event->media()->create([
                            'media_type' => 'image',
                            'media_url' => $path
                        ]);
                    }
                } elseif ($m['media_type'] === 'video' && !empty($m['url'])) {
                    $event->media()->create([
                        'media_type' => 'video',
                        'media_url' => $m['url']
                    ]);
                }
            }
        }
        $event->update($data); return $event;
    }


    public function destroy(Event $event){ $event->delete(); return response()->noContent(); }
}
