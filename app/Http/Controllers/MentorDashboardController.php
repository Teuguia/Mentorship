<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Review;
use App\Models\Session;

class MentorDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mentor = $user->mentor()->with('domains')->first();

        abort_unless($mentor, 403, 'Profil mentor introuvable.');

        $sessions = Session::query()
            ->with(['mentee.user'])
            ->where('mentor_id', $mentor->id)
            ->orderByDesc('scheduled_at')
            ->get();

        $upcomingSessions = $sessions
            ->where('scheduled_at', '>=', now())
            ->sortBy('scheduled_at')
            ->take(5)
            ->values();

        $activeMentees = $sessions
            ->filter(fn ($session) => $session->mentee && $session->mentee->user)
            ->unique('mentee_id')
            ->take(3)
            ->map(fn ($session) => $session->mentee)
            ->values();

        $availableMentees = $sessions
            ->filter(fn ($session) => $session->mentee && $session->mentee->user)
            ->unique('mentee_id')
            ->map(fn ($session) => $session->mentee)
            ->values();

        $recentReviews = Review::query()
            ->with(['mentee.user', 'session'])
            ->where('mentor_id', $mentor->id)
            ->where('reviewer_role', 'mentee')
            ->latest()
            ->take(4)
            ->get();

        $reviewsCount = Review::query()
            ->where('mentor_id', $mentor->id)
            ->where('reviewer_role', 'mentee')
            ->count();

        $recentConversations = Conversation::query()
            ->with(['mentee.user', 'latestMessage.sender'])
            ->where('mentor_id', $mentor->id)
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->take(4)
            ->get();

        $stats = [
            'upcoming_sessions' => $sessions
                ->where('scheduled_at', '>=', now())
                ->count(),
            'active_mentees' => $sessions
                ->unique('mentee_id')
                ->count(),
            'completed_sessions' => $sessions
                ->where('status', 'completed')
                ->count(),
            'reviews_count' => $reviewsCount,
        ];

        $reviewableSessions = Session::query()
            ->with(['mentee.user'])
            ->where('mentor_id', $mentor->id)
            ->where('status', 'completed')
            ->whereDoesntHave('reviews', fn ($query) => $query->where('reviewer_role', 'mentor'))
            ->orderByDesc('scheduled_at')
            ->get();

        return view('mentor.dashboard', compact(
            'mentor',
            'upcomingSessions',
            'activeMentees',
            'availableMentees',
            'recentReviews',
            'reviewableSessions',
            'recentConversations',
            'stats'
        ));
    }
}
