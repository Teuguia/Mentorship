<?php

namespace App\Http\Controllers;

use App\Models\Mentee;
use App\Models\Mentor;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardSessionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'contact_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        if ($user->role === 'mentor') {
            $mentor = $user->mentor;
            abort_unless($mentor, 403, 'Profil mentor introuvable.');

            $mentee = Mentee::query()->findOrFail($validated['contact_id']);

            Session::create([
                'mentor_id' => $mentor->id,
                'mentee_id' => $mentee->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'scheduled_at' => $validated['scheduled_at'],
                'status' => 'pending',
            ]);
        } else {
            $mentee = $user->mentee;
            abort_unless($mentee, 403, 'Profil mentee introuvable.');

            $mentor = Mentor::query()->findOrFail($validated['contact_id']);
            abort_unless($mentor->isVerified(), 403, 'Ce conseiller est encore en attente de verification.');

            Session::create([
                'mentor_id' => $mentor->id,
                'mentee_id' => $mentee->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'scheduled_at' => $validated['scheduled_at'],
                'status' => 'pending',
            ]);
        }

        return back()->with('status', 'Session de travail creee avec succes.');
    }
}
