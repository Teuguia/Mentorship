<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = Session::with(['mentor.user', 'mentee.user', 'review'])->latest()->get();

        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mentor_id' => ['required', 'exists:mentors,id'],
            'mentee_id' => ['required', 'exists:mentees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'status' => ['nullable', 'in:pending,confirmed,completed,cancelled'],
            'meeting_link' => ['nullable', 'url'],
            'notes' => ['nullable', 'string'],
            'document_path' => ['nullable', 'string'],
        ]);

        $session = Session::create([
            'mentor_id' => $validated['mentor_id'],
            'mentee_id' => $validated['mentee_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
            'status' => $validated['status'] ?? 'pending',
            'meeting_link' => $validated['meeting_link'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'document_path' => $validated['document_path'] ?? null,
        ]);

        return response()->json([
            'message' => 'Session created successfully',
            'data' => $session->load(['mentor.user', 'mentee.user']),
        ], 201);
    }

    public function show(Session $session)
    {
        return response()->json($session->load(['mentor.user', 'mentee.user', 'review']));
    }

    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'mentor_id' => ['required', 'exists:mentors,id'],
            'mentee_id' => ['required', 'exists:mentees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_at' => ['required', 'date'],
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
            'meeting_link' => ['nullable', 'url'],
            'notes' => ['nullable', 'string'],
            'document_path' => ['nullable', 'string'],
        ]);

        $session->update($validated);

        return response()->json([
            'message' => 'Session updated successfully',
            'data' => $session->load(['mentor.user', 'mentee.user', 'review']),
        ]);
    }

    public function destroy(Session $session)
    {
        $session->delete();

        return response()->json([
            'message' => 'Session deleted successfully',
        ]);
    }
}
