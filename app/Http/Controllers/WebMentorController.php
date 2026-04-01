<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Mentor;
use Illuminate\Http\Request;

class WebMentorController extends Controller
{
    public function index(Request $request)
    {
        $domains = Domain::orderBy('name')->get();

        $mentors = Mentor::query()
            ->with(['user', 'domains'])
            ->when($request->filled('domain_id'), function ($query) use ($request) {
                $query->whereHas('domains', function ($q) use ($request) {
                    $q->where('domains.id', $request->domain_id);
                });
            })
            ->when($request->filled('location'), function ($query) use ($request) {
                $query->where('availability', 'like', '%'.$request->location.'%');
            })
            ->when($request->filled('experience_min'), function ($query) use ($request) {
                $query->where('years_experience', '>=', (int) $request->experience_min);
            })
            ->when($request->filled('rate_min'), function ($query) use ($request) {
                $query->where('hourly_rate', '>=', (float) $request->rate_min);
            })
            ->when($request->filled('rate_max'), function ($query) use ($request) {
                $query->where('hourly_rate', '<=', (float) $request->rate_max);
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->q;

                $query->where(function ($q) use ($search) {
                    $q->where('expertise_title', 'like', '%'.$search.'%')
                        ->orWhere('bio', 'like', '%'.$search.'%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('mentors.index', compact('mentors', 'domains'));
    }

    public function show(Mentor $mentor)
    {
        $mentor->load(['user', 'domains', 'sessions.review']);

        return view('mentors.show', compact('mentor'));
    }
}
