<?php

namespace App\Http\Controllers;
use App\Models\PortfolioItem;
use Illuminate\Http\Request;



class PortfolioItemController extends Controller {
    public function index(){
        $items = PortfolioItem::with('subjects.media')->get();

        return response()->json([
            'data' => $items
        ]);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $item = PortfolioItem::create($validated);

        return response()->json($item);
    }
    public function show(PortfolioItem $portfolioItem){ return $portfolioItem; }
    public function update(Request $r, PortfolioItem $portfolioItem){
        $data = $r->validate([
                'title'=>'sometimes|required','description'=>'nullable']
        );
        $portfolioItem->update($data); return $portfolioItem;
    }
    public function destroy(PortfolioItem $portfolioItem){ $portfolioItem->delete(); return response()->noContent(); }
}
