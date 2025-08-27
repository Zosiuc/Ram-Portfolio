<?php

namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use App\Models\Visit;


class TrackVisits{
    public function handle(Request $r, Closure $next){
        $res = $next($r);
        if ($r->isMethod('get') && !str_starts_with($r->path(),'api')){
            \App\Models\Visit::create(['path' => '/'.$r->path(),'ip'=>$r->ip(),'ua'=>$r->userAgent()]);
        }
        return $res;
    }
}
