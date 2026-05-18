<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebReviewController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'session_id' => ['required', 'integer', 'exists:sessions,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $session = Session::query()->with(['mentor.user', 'mentee.user'])->findOrFail($validated['session_id']);

        $isMentor = $user->role === 'mentor' && $session->mentor_id === $user->mentor?->id;
        $isMentee = $user->role === 'mentee' && $session->mentee_id === $user->mentee?->id;

        abort_unless($isMentor || $isMentee, 403);

        if ($session->status !== 'completed') {
            return back()->withErrors([
                'review' => 'Un avis ne peut etre laisse que pour une session terminee.',
            ])->withInput();
        }

        $reviewerRole = $isMentor ? 'mentor' : 'mentee';

        Review::updateOrCreate(
            [
                'session_id' => $session->id,
                'reviewer_role' => $reviewerRole,
            ],
            [
                'mentor_id' => $session->mentor_id,
                'mentee_id' => $session->mentee_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return back()->with('status', 'Avis enregistre avec succes.');
    }
}
