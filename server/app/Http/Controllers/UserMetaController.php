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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'bio' => 'nullable|string',
            'number' => 'nullable|string',
            'email' => 'nullable|string|email',
            'profile_photo' => 'nullable|file|image|max:2048',
            'cover_photo' => 'nullable|file|image|max:2048',
            'education' => 'array',
            'experience' => 'array',
            'skills' => 'array',
            'social_media' => 'array',
            'reels' => 'array',
        ]);

        // Save profile & cover photos
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('user_meta', 'public');
        }
        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('user_meta', 'public');
        }

        $user_meta = UserMeta::update($validated);

        // Education
        if ($request->has('education')) {
            foreach ($request->education as $edu) {
                $user_meta->education()->create($edu);
            }
        }

        // Experience
        if ($request->has('experience')) {
            foreach ($request->experience as $exp) {
                $user_meta->experience()->create($exp);
            }
        }

        // Skills
        if ($request->has('skills')) {
            foreach ($request->skills as $skill) {
                $user_meta->skills()->create($skill);
            }
        }

        // Social Media
        if ($request->has('social_media')) {
            foreach ($request->social_media as $sm) {
                $user_meta->socialMedia()->create($sm);
            }
        }

        // Reel
        if ($request->has('reels')) {
            foreach ($request->reels as $reel) {
                $user_meta->reels()->create($reel);
            }
        }

        return response()->json($user_meta->load('education', 'experience', 'skills', 'socialMedia', 'reels'), 201);
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
        }

        return response()->json($userMeta);
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
            'education.*.id' => 'nullable|integer',
            'education.*.title' => 'nullable|string',
            'education.*.description' => 'nullable|string',
            'education.*.start_date' => 'nullable|date',
            'education.*.end_date' => 'nullable|date',

            'experience' => 'nullable|array',
            'experience.*.id' => 'nullable|integer',
            'experience.*.title' => 'nullable||string',
            'experience.*.description' => 'nullable|string',
            'experience.*.company_name' => 'nullable|string',
            'experience.*.start_date' => 'nullable|date',
            'experience.*.end_date' => 'nullable|date',

            'skills' => 'nullable|array',
            'skills.*.id' => 'nullable|integer',
            'skills.*.title' => 'nullable||string',
            'skills.*.description' => 'nullable|string',

            'social_media' => 'nullable|array',
            'social_media.*.id' => 'nullable|integer',
            'social_media.*.title' => 'nullable||string',
            'social_media.*.description' => 'nullable|string',
            'social_media.*.url' => 'nullable|url',

            'reels' => 'nullable|array',
            'reels.*.id' => 'nullable|integer',
            'reels.*.title' => 'nullable||string',
            'reels.*.description' => 'nullable|string',
        ]);


        // Save profile & cover photos
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('user_meta', 'public');
        }
        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('user_meta', 'public');
        }

        if (!$request->has('id')) {
            $user_meta = UserMeta::create($validated);
        } else {
            $user_meta->update($validated);
        }
        // Education
        $educations = $request->input('education', []); // fallback naar lege array
        foreach ($educations as $edu) {
            if (!is_array($edu)) continue; // skip als het geen array is

            $attributes = [];
            if (!empty($edu['id'])) $attributes['id'] = $edu['id'];

            $user_meta->education()->updateOrCreate(
                $attributes,
                $edu
            );
            \Log::info('Education item', [$edu]);
        }

        // Experience
        $experience = $request->input('experience', []); // fallback naar lege array
        foreach ($experience as $ex) {
            if (!is_array($ex)) continue; // skip als het geen array is

            $attributes = [];
            if (!empty($ex['id'])) $attributes['id'] = $ex['id'];

            $user_meta->experience()->updateOrCreate(
                $attributes,
                $ex
            );
            \Log::info('Experience item', [$ex]);
        }

        // Skills
        $skills = $request->input('skills', []); // fallback naar lege array
        foreach ($skills as $skill) {
            if (!is_array($skill)) continue; // skip als het geen array is

            $attributes = [];
            if (!empty($skill['id'])) $attributes['id'] = $skill['id'];

            $user_meta->skills()->updateOrCreate(
                $attributes,
                $skill
            );
            \Log::info('skills item', [$skill]);

            // Social Media
            $socialMedia = $request->input('socialMedia', []); // fallback naar lege array
            foreach ($socialMedia as $sm) {
                if (!is_array($sm)) continue; // skip als het geen array is

                $attributes = [];
                if (!empty($sm['id'])) $attributes['id'] = $sm['id'];

                $user_meta->socialMedia()->updateOrCreate(
                    $attributes,
                    $sm
                );
                \Log::info('Social media item', [$sm]);
            }

            // Reel
            $reels = $request->input('reels', []); // fallback naar lege array
            foreach ($reels as $reel) {
                if (!is_array($reel)) continue; // skip als het geen array is

                $attributes = [];
                if (!empty($reel['id'])) $attributes['id'] = $reel['id'];

                $user_meta->reels()->updateOrCreate(
                    $attributes,
                    $reel
                );
                \Log::info('reels item', [$reel]);
            }

            \Log::info($request->all($user_meta));
        }

            return response()->json($user_meta->load('education', 'experience', 'skills', 'socialMedia', 'reels'), 201);
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


