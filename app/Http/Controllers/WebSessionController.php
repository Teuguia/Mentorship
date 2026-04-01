<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WebSessionController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $sessions = Session::query()
            ->with(['mentor.user', 'mentee.user', 'review'])
            ->when($user->role === 'mentor', fn ($query) => $query->where('mentor_id', $user->mentor?->id))
            ->when($user->role === 'mentee', fn ($query) => $query->where('mentee_id', $user->mentee?->id))
            ->orderBy('scheduled_at')
            ->get()
            ->map(function (Session $session) {
                $session->conversation = Conversation::query()
                    ->where('mentor_id', $session->mentor_id)
                    ->where('mentee_id', $session->mentee_id)
                    ->first();

                return $session;
            });

        return view('sessions.index', compact('sessions'));
    }
}
