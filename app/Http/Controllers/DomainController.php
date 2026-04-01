<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::latest()->get();

        return response()->json($domains);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:domains,name'],
            'description' => ['nullable', 'string'],
        ]);

        $domain = Domain::create($validated);

        return response()->json([
            'message' => 'Domain created successfully',
            'data' => $domain,
        ], 201);
    }

    public function show(Domain $domain)
    {
        return response()->json($domain->load(['mentors.user', 'mentees.user']));
    }

    public function update(Request $request, Domain $domain)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('domains', 'name')->ignore($domain->id)],
            'description' => ['nullable', 'string'],
        ]);

        $domain->update($validated);

        return response()->json([
            'message' => 'Domain updated successfully',
            'data' => $domain,
        ]);
    }

    public function destroy(Domain $domain)
    {
        $domain->delete();

        return response()->json([
            'message' => 'Domain deleted successfully',
        ]);
    }

    public function mentors(Domain $domain)
    {
        $mentors = $domain->mentors()->with(['user', 'domains'])->get();

        return response()->json($mentors);
    }
}
