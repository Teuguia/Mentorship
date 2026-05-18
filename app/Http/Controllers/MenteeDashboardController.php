<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Review;
use App\Models\Session;

class MenteeDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mentee = $user->mentee()->with('domains')->first();

        abort_unless($mentee, 403, 'Profil mentee introuvable.');

        $sessions = Session::query()
            ->with(['mentor.user'])
            ->where('mentee_id', $mentee->id)
            ->orderByDesc('scheduled_at')
            ->get();

        $upcomingSessions = $sessions
            ->where('scheduled_at', '>=', now())
            ->sortBy('scheduled_at')
            ->take(5)
            ->values();

        $recentReviews = Review::query()
            ->with(['mentor.user', 'session'])
            ->where('mentee_id', $mentee->id)
            ->where('reviewer_role', 'mentor')
            ->latest()
            ->take(4)
            ->get();

        $recentConversations = Conversation::query()
            ->with(['mentor.user', 'latestMessage.sender'])
            ->where('mentee_id', $mentee->id)
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->take(4)
            ->get();

        $domains = $mentee->domains()->orderBy('name')->get();

        $availableMentors = $sessions
            ->filter(fn ($session) => $session->mentor && $session->mentor->user)
            ->unique('mentor_id')
            ->map(fn ($session) => $session->mentor)
            ->values();

        $stats = [
            'upcoming_sessions' => $sessions
                ->where('scheduled_at', '>=', now())
                ->count(),
            'active_mentors' => $sessions
                ->unique('mentor_id')
                ->count(),
            'completed_sessions' => $sessions
                ->where('status', 'completed')
                ->count(),
            'conversations_count' => Conversation::query()
                ->where('mentee_id', $mentee->id)
                ->count(),
        ];

        $reviewableSessions = Session::query()
            ->with(['mentor.user'])
            ->where('mentee_id', $mentee->id)
            ->where('status', 'completed')
            ->whereDoesntHave('reviews', fn ($query) => $query->where('reviewer_role', 'mentee'))
            ->orderByDesc('scheduled_at')
            ->get();

        return view('mentee.dashboard', compact(
            'mentee',
            'upcomingSessions',
            'recentReviews',
            'recentConversations',
            'domains',
            'availableMentors',
            'reviewableSessions',
            'stats'
        ));
    }
}
