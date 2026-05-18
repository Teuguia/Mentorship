<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MentorVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $status = $request->query('status');

        $mentors = Mentor::query()
            ->with(['user', 'verifier'])
            ->when(in_array($status, ['pending', 'verified', 'rejected'], true), fn ($query) => $query->where('verification_status', $status))
            ->orderByRaw("case verification_status when 'pending' then 0 when 'rejected' then 1 else 2 end")
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'pending' => Mentor::where('verification_status', 'pending')->count(),
            'verified' => Mentor::where('verification_status', 'verified')->count(),
            'rejected' => Mentor::where('verification_status', 'rejected')->count(),
        ];

        return view('admin.mentor-verifications.index', compact('mentors', 'counts', 'status'));
    }

    public function update(Request $request, Mentor $mentor): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'verification_status' => ['required', 'in:verified,rejected'],
            'verification_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (
            $validated['verification_status'] === 'verified'
            && (! $mentor->verification_document || (! $mentor->linkedin_url && ! $mentor->portfolio_url))
        ) {
            return back()->withErrors([
                'verification_status' => 'Un profil valide doit avoir un justificatif et au moins un lien LinkedIn ou portfolio.',
            ]);
        }

        $mentor->forceFill([
            'verification_status' => $validated['verification_status'],
            'verification_notes' => $validated['verification_notes'] ?? null,
            'verified_at' => $validated['verification_status'] === 'verified' ? now() : null,
            'verified_by' => $validated['verification_status'] === 'verified' ? $request->user()->id : null,
        ])->save();

        return back()->with('status', 'Statut de verification mis a jour.');
    }

    public function document(Request $request, Mentor $mentor): Response
    {
        $this->authorizeAdmin($request);

        abort_unless($mentor->verification_document, 404);

        return response($mentor->verification_document, 200, [
            'Content-Type' => $mentor->verification_document_mime ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$mentor->verification_document_name.'"',
        ]);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Acces reserve aux admins.');
    }
}
