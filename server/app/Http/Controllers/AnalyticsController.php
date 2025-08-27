<?php

namespace App\Http\Controllers; use Illuminate\Support\Facades\DB; use Carbon\Carbon;


class AnalyticsController extends Controller{
    public function summary(){
        $since = now()->subDays(30);
        $daily = DB::table('visits')
            ->selectRaw('DATE(created_at) as day, count(*) as visits')
            ->where('created_at','>=',$since)
            ->groupBy('day')->orderBy('day')->get();
        return ['last30d'=> $daily];
    }
}
