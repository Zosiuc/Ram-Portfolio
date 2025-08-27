<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller {
    public function login(Request $r) {
        $cred = $r->validate(['email'=>'required|email','password'=>'required']);

        if (!Auth::attempt($cred)) return response()->json(['message'=>'Invalid credentials'], 401);
        $r->session()->regenerate();
        return response()->json(['user'=>Auth::user()]);
    }
    public function me(Request $r){ return $r->user(); }
    public function logout(Request $r){ Auth::guard('web')->logout(); $r->session()->invalidate(); $r->session()->regenerateToken(); return response()->noContent(); }
}
