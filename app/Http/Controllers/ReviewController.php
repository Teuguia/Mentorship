<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Models\Review;
use App\Models\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['required', 'integer', 'exists:coaching_sessions,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);

        $session = Session::query()->findOrFail($data['session_id']);

        if ($session->status !== 'completed') {
            return response()->json([
                'message' => 'Une review est possible uniquement pour une session terminee.',
            ], 422);
        }

        $review = Review::create([
            'session_id' => $session->id,
            'mentor_id' => $session->mentor_id,
            'mentee_id' => $session->mentee_id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return response()->json($review->load(['mentor.user', 'mentee.user', 'session']), 201);
    }

    public function byMentor(Mentor $mentor): JsonResponse
    {
        $reviews = Review::query()
            ->where('mentor_id', $mentor->id)
            ->with(['mentee.user', 'session'])
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }
}
