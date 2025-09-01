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
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cover_photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'education' => 'nullable|array',
            'education.*.id' => 'nullable|integer',
            'education.*.title' => 'required|string',
            'education.*.description' => 'nullable|string',
            'education.*.start_date' => 'nullable|date',
            'education.*.end_date' => 'nullable|date',

            'experience' => 'nullable|array',
            'experience.*.id' => 'nullable|integer',
            'experience.*.title' => 'required|string',
            'experience.*.description' => 'nullable|string',
            'experience.*.company_name' => 'nullable|string',
            'experience.*.start_date' => 'nullable|date',
            'experience.*.end_date' => 'nullable|date',

            'skills' => 'nullable|array',
            'skills.*.id' => 'nullable|integer',
            'skills.*.title' => 'required|string',
            'skills.*.description' => 'nullable|string',

            'social_media' => 'nullable|array',
            'social_media.*.id' => 'nullable|integer',
            'social_media.*.title' => 'required|string',
            'social_media.*.description' => 'nullable|string',
            'social_media.*.url' => 'nullable|url',

            'reels' => 'nullable|array',
            'reels.*.id' => 'nullable|integer',
            'reels.*.title' => 'required|string',
            'reels.*.description' => 'nullable|string',
            'reels.*.url' => 'nullable|url',
        ]);


        // Save profile & cover photos
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('user_meta', 'public');
        }
        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('user_meta', 'public');
        }

        $user_meta = UserMeta::create($validated);

        // Education
        if ($request->has('education')) {
            $ids = [];
            foreach ($request->education as $edu) {
                $education = $user_meta->education()->updateOrCreate(
                    ['id' => $edu['id'] ?? null], // zoek op id
                    $edu
                );
                $ids[] = $education->id;
            }
            // verwijder records die niet meer in de request zitten
            $user_meta->education()->whereNotIn('id', $ids)->delete();
        }

        // Experience
        if ($request->has('experience')) {
            $ids = [];
            foreach ($request->experience as $ex) {
                $experience = $user_meta->experience()->updateOrCreate(
                    ['id' => $ex['id'] ?? null], // zoek op id
                    $ex
                );
                $ids[] = $experience->id;
            }
            // verwijder records die niet meer in de request zitten
            $user_meta->experience()->whereNotIn('id', $ids)->delete();
        }

        // Skills
        if ($request->has('skills')) {
            $ids = [];
            foreach ($request->skills as $sk) {
                $skills = $user_meta->skills()->updateOrCreate(
                    ['id' => $sk['id'] ?? null], // zoek op id
                    $sk
                );
                $ids[] = $skills->id;
            }
            // verwijder records die niet meer in de request zitten
            $user_meta->skills()->whereNotIn('id', $ids)->delete();
        }

        // Social Media
        if ($request->has('social_media')) {
            $ids = [];
            foreach ($request->socialMedia as $sm) {
                $socialMedia = $user_meta->socialMedia()->updateOrCreate(
                    ['id' => $sm['id'] ?? null], // zoek op id
                    $sm
                );
                $ids[] = $socialMedia->id;
            }
            // verwijder records die niet meer in de request zitten
            $user_meta->$socialMedia()->whereNotIn('id', $ids)->delete();
        }

        // Reel
        if ($request->has('reels')) {
            $ids = [];
            foreach ($request->reels as $reel) {
                $reels = $user_meta->reels()->updateOrCreate(
                    ['id' => $reel['id'] ?? null], // zoek op id
                    $reel
                );
                $ids[] = $reel->id;
            }
            // verwijder records die niet meer in de request zitten
            $user_meta->reels()->whereNotIn('id', $ids)->delete();
        }

        return response()->json($user_meta->load('education','experience','skills','socialMedia','reels'), 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(UserMeta $userMeta)
    {
        return response()->json(
            $userMeta->load(['education', 'experience', 'skills', 'socialMedia', 'reels'])
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserMeta $userMeta)
    {
        return response()->json(['message' => 'UserMeta edit form (frontend)']);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, UserMeta $user_meta)

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
            'education' => 'array',
            'experience' => 'array',
            'skills' => 'array',
            'social_media' => 'array',
            'reels' => 'array',
        ]);

        // ✅ Update profile & cover photos
        if ($request->hasFile('profile_photo')) {
            $file =$request->file('profile_photo');
            if($file) {
                $path = $file->store('user_meta', 'public');
                $validated['profile_photo'] = $path;
            }
        }
        if ($request->hasFile('cover_photo')) {
            $file =$request->file('cover_photo');
            if($file) {
                $path = $file->store('user_meta', 'public');
                $validated['cover_photo'] = $path;
            }

        }

        $user_meta->update($validated);



        // Education

        if ($request->has('education')) {
            $user_meta->education()->delete();
            foreach ($request->education as $edu) {
                $user_meta->education()->updateOrCreate(['id' => $edu['id'] ?? null],$edu);
            }
        }else $user_meta->education()->delete();

        // Experience

        if ($request->has('experience')) {
            $user_meta->experience()->delete();
            foreach ($request->experience as $exp) {
                $user_meta->experience()->updateOrCreate(['id' => $exp['id'] ?? null],$exp);
            }
        }else $user_meta->experience()->delete();

        // Skills

        if ($request->has('skills')) {
            $user_meta->skills()->delete();
            foreach ($request->skills as $skill) {
                $user_meta->skills()->updateOrCreate(['id' => $skill['id'] ?? null],$skill);
            }
        }else $user_meta->skills()->delete();

        // Social Media

        if ($request->has('social_media')) {
            $user_meta->socialMedia()->delete();
            foreach ($request->social_media as $sm) {
                $user_meta->socialMedia()->updateOrCreate(['id' => $sm['id'] ?? null],$sm);
            }
        }else $user_meta->socialMedia()->delete();

        // Reel
        if ($request->has('reels')) {
            $user_meta->reels()->delete();
            foreach ($request->reels as $reel) {
                $user_meta->reels()->updateOrCreate(['id' => $reel['id'] ?? null],$reel);
            }
        }else $user_meta->reels()->delete();


        return response()->json(
            $user_meta->load(['education', 'experience', 'skills', 'socialMedia', 'reels'])
        );

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserMeta $userMeta)
    {
        $userMeta->delete();
        return response()->json(null, 204);
    }
}
