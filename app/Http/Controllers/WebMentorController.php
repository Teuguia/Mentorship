<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Mentor;
use Illuminate\Http\Request;

class WebMentorController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'domain_id' => ['nullable', 'integer', 'exists:domains,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:255'],
            'experience_min' => ['nullable', 'integer', 'min:0'],
            'rate_min' => ['nullable', 'numeric', 'min:0'],
            'rate_max' => ['nullable', 'numeric', 'min:0'],
        ]);

        $domains = Domain::orderBy('name')->get();
        $locations = Mentor::query()
            ->whereNotNull('availability')
            ->where('availability', '!=', '')
            ->distinct()
            ->orderBy('availability')
            ->pluck('availability');

        $locationAliases = $this->normalizeLocationAliases($filters['location'] ?? null);

        $mentors = Mentor::query()
            ->with(['user', 'domains'])
            ->when(! empty($filters['domain_id']), function ($query) use ($filters) {
                $query->whereHas('domains', function ($domainQuery) use ($filters) {
                    $domainQuery->where('domains.id', $filters['domain_id']);
                });
            })
            ->when(! empty($locationAliases), function ($query) use ($locationAliases) {
                $query->where(function ($locationQuery) use ($locationAliases) {
                    foreach ($locationAliases as $location) {
                        $locationQuery->orWhere('availability', 'like', '%'.$location.'%');
                    }
                });
            })
            ->when(isset($filters['experience_min']) && $filters['experience_min'] !== null, function ($query) use ($filters) {
                $query->where('years_experience', '>=', (int) $filters['experience_min']);
            })
            ->when(isset($filters['rate_min']) && $filters['rate_min'] !== null, function ($query) use ($filters) {
                $query->where('hourly_rate', '>=', (float) $filters['rate_min']);
            })
            ->when(isset($filters['rate_max']) && $filters['rate_max'] !== null, function ($query) use ($filters) {
                $query->where('hourly_rate', '<=', (float) $filters['rate_max']);
            })
            ->when(! empty($filters['q']), function ($query) use ($filters) {
                $search = trim($filters['q']);

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('expertise_title', 'like', '%'.$search.'%')
                        ->orWhere('bio', 'like', '%'.$search.'%')
                        ->orWhereHas('domains', function ($domainQuery) use ($search) {
                            $domainQuery->where('name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('mentors.index', compact('mentors', 'domains', 'locations'));
    }

    public function show(Mentor $mentor)
    {
        $mentor->load(['user', 'domains', 'sessions.review']);

        return view('mentors.show', compact('mentor'));
    }

    private function normalizeLocationAliases(?string $location): array
    {
        if (! $location) {
            return [];
        }

        $normalized = mb_strtolower(trim($location));

        $aliases = [
            'online' => ['Online', 'En ligne'],
            'en ligne' => ['Online', 'En ligne'],
            'remote' => ['Remote', 'distance'],
            'a distance' => ['Remote', 'distance'],
            'distance' => ['Remote', 'distance'],
            'douala' => ['Douala'],
            'yaounde' => ['Yaounde', 'Yaound'],
        ];

        return $aliases[$normalized] ?? [$location];
    }
}
