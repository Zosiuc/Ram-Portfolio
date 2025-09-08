<?php

namespace App\Http\Controllers;


use App\Models\UserMeta;
use Illuminate\Http\Request;

class UserMetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Haal alle users met hun relaties op
        return response()->json(
            UserMeta::with(['education', 'experience', 'skills', 'socialMedia', 'reels'])->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'UserMeta create form (frontend)']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'bio' => 'nullable|string',
            'education_bio' => 'nullable|string',
            'number' => 'nullable|string',
            'email' => 'nullable|string|email',
            'birthday' => 'nullable|date',

            'education' => 'nullable|array',
            'education.*.title' => 'nullable|string',
            'education.*.description' => 'nullable|string',
            'education.*.start_date' => 'nullable|date',
            'education.*.end_date' => 'nullable|date',

            'experience' => 'nullable|array',
            'experience.*.title' => 'nullable|string',
            'experience.*.description' => 'nullable|string',
            'experience.*.company_name' => 'nullable|string',
            'experience.*.start_date' => 'nullable|date',
            'experience.*.end_date' => 'nullable|date',

            'skills' => 'nullable|array',
            'skills.*.title' => 'nullable|string',
            'skills.*.description' => 'nullable|string',

            'social_media' => 'nullable|array',
            'social_media.*.title' => 'nullable|string',
            'social_media.*.description' => 'nullable|string',
            'social_media.*.url' => 'nullable|url',

            'reels' => 'nullable|array',
            'reels.*.title' => 'nullable|string',
            'reels.*.description' => 'nullable|string',
        ]);

        // Save profile & cover photos
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('user_meta', 'public');
        }
        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('user_meta', 'public');
        }

        // Create main UserMeta record
        $user_meta = UserMeta::create($validated);

        // Education
        foreach ($request->input('education', []) as $edu) {
            $user_meta->education()->create($edu);
        }

        // Experience
        foreach ($request->input('experience', []) as $ex) {
            $user_meta->experience()->create($ex);
        }

        // Skills
        foreach ($request->input('skills', []) as $skill) {
            $user_meta->skills()->create($skill);
        }

        // Social Media
        foreach ($request->input('social_media', []) as $sm) {
            $user_meta->socialMedia()->create($sm);
        }

        // Reels
        foreach ($request->input('reels', []) as $reel) {
            $user_meta->reels()->create($reel);
        }

        return response()->json(
            $user_meta->load('education', 'experience', 'skills', 'socialMedia', 'reels'),
            201
        );
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        \Log::info($id);
        $userMeta = UserMeta::with(['education', 'experience', 'skills', 'socialMedia', 'reels'])->find($id);

        if (!$id) {
            return response()->json(['message' => 'UserMeta not found'], 404);
        }else return response()->json($userMeta);
    }


    protected function syncRelations(UserMeta $user_meta, Request $request, array $relations)
    {
        foreach ($relations as $relation) {
            $items = $request->input($relation, []);

            // Verzamel alle IDs die meegestuurd zijn
            $ids = collect($items)->pluck('id')->filter()->toArray();

            // Verwijder alles wat niet meer in de request zit
            $user_meta->$relation()->whereNotIn('id', $ids)->delete();

            // Update of create de resterende items
            foreach ($items as $item) {
                if (!empty($item['id'])) {
                    $user_meta->$relation()->updateOrCreate(
                        ['id' => $item['id']],
                        $item
                    );
                } else {
                    $user_meta->$relation()->create($item);
                }
            }
        }

    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, UserMeta $user_meta)
    {
        \Log::info($request->all());

        $validated = $request->validate([
            'job_title' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'bio' => 'nullable|string',
            'education_bio' => 'nullable|string',
            'number' => 'nullable|string',
            'email' => 'nullable|string|email',
            'birthday' => 'nullable|date',

            'education' => 'nullable|array',

            'experience' => 'nullable|array',

            'skills' => 'nullable|array',

            'social_media' => 'nullable|array',

            'reels' => 'nullable|array',
        ]);

        // Save profile & cover photos
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('user_meta', 'public');
        }
        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('user_meta', 'public');
        }

        $user_meta->update($validated);
        \Log::info('Updated UserMeta: ' . $user_meta->id);

        $this->syncRelations($user_meta, $request,
            ['education', 'experience', 'skills', 'socialMedia', 'reels']);

        return response()->json(
            $user_meta->load('education', 'experience', 'skills', 'socialMedia', 'reels'),
            200
        );
    }



    /**
     * Remove the specified resource from storage.
     */
    public
    function destroy(UserMeta $userMeta)
    {
        $userMeta->delete();
        return response()->json(null, 204);
    }
}


