<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MentorController extends Controller
{
    public function index()
    {
        $mentors = Mentor::with(['user', 'domains'])->latest()->get();

        return response()->json($mentors);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:mentors,user_id'],
            'bio' => ['nullable', 'string'],
            'expertise_title' => ['required', 'string', 'max:255'],
            'years_experience' => ['required', 'integer', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'availability' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'string'],
            'domains' => ['nullable', 'array'],
            'domains.*' => ['exists:domains,id'],
        ]);

        $mentor = Mentor::create([
            'user_id' => $validated['user_id'],
            'bio' => $validated['bio'] ?? null,
            'expertise_title' => $validated['expertise_title'],
            'years_experience' => $validated['years_experience'],
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'availability' => $validated['availability'] ?? null,
            'profile_photo' => $validated['profile_photo'] ?? null,
        ]);

        if (!empty($validated['domains'])) {
            $mentor->domains()->sync($validated['domains']);
        }

        return response()->json([
            'message' => 'Mentor created successfully',
            'data' => $mentor->load(['user', 'domains']),
        ], 201);
    }

    public function show(Mentor $mentor)
    {
        return response()->json($mentor->load(['user', 'domains', 'sessions.review']));
    }

    public function update(Request $request, Mentor $mentor)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', Rule::unique('mentors', 'user_id')->ignore($mentor->id)],
            'bio' => ['nullable', 'string'],
            'expertise_title' => ['required', 'string', 'max:255'],
            'years_experience' => ['required', 'integer', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'availability' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'string'],
            'domains' => ['nullable', 'array'],
            'domains.*' => ['exists:domains,id'],
        ]);

        $mentor->update([
            'user_id' => $validated['user_id'],
            'bio' => $validated['bio'] ?? null,
            'expertise_title' => $validated['expertise_title'],
            'years_experience' => $validated['years_experience'],
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'availability' => $validated['availability'] ?? null,
            'profile_photo' => $validated['profile_photo'] ?? null,
        ]);

        if (array_key_exists('domains', $validated)) {
            $mentor->domains()->sync($validated['domains'] ?? []);
        }

        return response()->json([
            'message' => 'Mentor updated successfully',
            'data' => $mentor->load(['user', 'domains']),
        ]);
    }

    public function destroy(Mentor $mentor)
    {
        $mentor->delete();

        return response()->json([
            'message' => 'Mentor deleted successfully',
        ]);
    }
}
