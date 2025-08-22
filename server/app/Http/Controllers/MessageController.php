<?php

namespace App\Http\Controllers;
use App\Models\Message; use Illuminate\Http\Request;


class MessageController extends Controller {
    public function store(Request $r){
        $data = $r->validate(['name'=>'required','email'=>'required|email','message'=>'required']);
        return Message::create($data);
    }

    public function index(){ return Message::latest()->paginate(20); }
}
