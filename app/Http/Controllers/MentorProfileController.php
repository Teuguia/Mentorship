<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MentorProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $mentor = $request->user()->mentor;

        abort_unless($request->user()->role === 'mentor' && $mentor, 403, 'Profil mentor introuvable.');

        $validated = $request->validate([
            'expertise_title' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:80'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'availability' => ['nullable', 'string', 'max:1000'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'verification_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $documentUploaded = $request->hasFile('verification_document');

        $mentor->fill([
            'expertise_title' => $validated['expertise_title'],
            'bio' => $validated['bio'] ?? null,
            'years_experience' => $validated['years_experience'],
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'availability' => $validated['availability'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
        ]);

        if ($documentUploaded) {
            $document = $request->file('verification_document');

            $mentor->forceFill([
                'verification_document' => file_get_contents($document->getRealPath()),
                'verification_document_name' => $document->getClientOriginalName(),
                'verification_document_mime' => $document->getClientMimeType(),
                'verification_document_size' => $document->getSize(),
                'verification_status' => 'pending',
                'verification_notes' => null,
                'verified_at' => null,
                'verified_by' => null,
            ]);
        } elseif ($mentor->verification_status === 'rejected') {
            $mentor->verification_status = 'pending';
            $mentor->verification_notes = null;
        }

        $mentor->save();

        return back()->with('status', 'Profil conseiller mis a jour. Votre verification sera traitee par un admin.');
    }
}
