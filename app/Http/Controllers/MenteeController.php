<?php

namespace App\Http\Controllers;

use App\Models\Mentee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenteeController extends Controller
{
    public function index()
    {
        $mentees = Mentee::with(['user', 'domains'])->latest()->get();

        return response()->json($mentees);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:mentees,user_id'],
            'goals' => ['nullable', 'string'],
            'profession' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'domains' => ['nullable', 'array'],
            'domains.*' => ['exists:domains,id'],
        ]);

        $mentee = Mentee::create([
            'user_id' => $validated['user_id'],
            'goals' => $validated['goals'] ?? null,
            'profession' => $validated['profession'] ?? null,
            'level' => $validated['level'] ?? null,
        ]);

        if (!empty($validated['domains'])) {
            $mentee->domains()->sync($validated['domains']);
        }

        return response()->json([
            'message' => 'Mentee created successfully',
            'data' => $mentee->load(['user', 'domains']),
        ], 201);
    }

    public function show(Mentee $mentee)
    {
        return response()->json($mentee->load(['user', 'domains', 'sessions.review']));
    }

    public function update(Request $request, Mentee $mentee)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', Rule::unique('mentees', 'user_id')->ignore($mentee->id)],
            'goals' => ['nullable', 'string'],
            'profession' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'domains' => ['nullable', 'array'],
            'domains.*' => ['exists:domains,id'],
        ]);

        $mentee->update([
            'user_id' => $validated['user_id'],
            'goals' => $validated['goals'] ?? null,
            'profession' => $validated['profession'] ?? null,
            'level' => $validated['level'] ?? null,
        ]);

        if (array_key_exists('domains', $validated)) {
            $mentee->domains()->sync($validated['domains'] ?? []);
        }

        return response()->json([
            'message' => 'Mentee updated successfully',
            'data' => $mentee->load(['user', 'domains']),
        ]);
    }

    public function destroy(Mentee $mentee)
    {
        $mentee->delete();

        return response()->json([
            'message' => 'Mentee deleted successfully',
        ]);
    }
}
