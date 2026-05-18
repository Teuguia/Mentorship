<x-layouts.marketing>
    <div class="min-h-screen bg-slate-100">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Verification des conseillers</h1>
                    <p class="mt-1 text-sm text-slate-500">Validez les profils avant leur affichage public.</p>
                </div>
                <a href="{{ route('home') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Retour au site
                </a>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mb-6 flex flex-wrap gap-3">
                <a href="{{ route('admin.mentor-verifications.index') }}" class="rounded-lg px-4 py-2 text-sm font-semibold {{ ! $status ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 hover:bg-slate-50' }}">
                    Tous
                </a>
                <a href="{{ route('admin.mentor-verifications.index', ['status' => 'pending']) }}" class="rounded-lg px-4 py-2 text-sm font-semibold {{ $status === 'pending' ? 'bg-amber-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50' }}">
                    En attente ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('admin.mentor-verifications.index', ['status' => 'verified']) }}" class="rounded-lg px-4 py-2 text-sm font-semibold {{ $status === 'verified' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50' }}">
                    Valides ({{ $counts['verified'] }})
                </a>
                <a href="{{ route('admin.mentor-verifications.index', ['status' => 'rejected']) }}" class="rounded-lg px-4 py-2 text-sm font-semibold {{ $status === 'rejected' ? 'bg-red-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50' }}">
                    A corriger ({{ $counts['rejected'] }})
                </a>
            </div>

            <div class="grid gap-5">
                @forelse($mentors as $mentor)
                    <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
                            <div>
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <h2 class="text-lg font-bold text-slate-900">{{ $mentor->user->name ?? 'Conseiller' }}</h2>
                                        <p class="text-sm text-slate-500">{{ $mentor->user->email ?? '' }}</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-800">{{ $mentor->expertise_title }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $mentor->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-700' : ($mentor->verification_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ $mentor->verificationStatusLabel() }}
                                    </span>
                                </div>

                                <p class="mt-4 text-sm leading-6 text-slate-600">
                                    {{ $mentor->bio ?: 'Aucune bio renseignee.' }}
                                </p>

                                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <dt class="text-xs font-semibold uppercase text-slate-400">Experience</dt>
                                        <dd class="mt-1 font-semibold text-slate-800">{{ $mentor->years_experience }} ans</dd>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <dt class="text-xs font-semibold uppercase text-slate-400">LinkedIn</dt>
                                        <dd class="mt-1 truncate">
                                            @if($mentor->linkedin_url)
                                                <a href="{{ $mentor->linkedin_url }}" target="_blank" rel="noreferrer" class="font-semibold text-blue-600 hover:text-blue-700">Ouvrir</a>
                                            @else
                                                <span class="text-slate-500">Non renseigne</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <dt class="text-xs font-semibold uppercase text-slate-400">Portfolio</dt>
                                        <dd class="mt-1 truncate">
                                            @if($mentor->portfolio_url)
                                                <a href="{{ $mentor->portfolio_url }}" target="_blank" rel="noreferrer" class="font-semibold text-blue-600 hover:text-blue-700">Ouvrir</a>
                                            @else
                                                <span class="text-slate-500">Non renseigne</span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>

                                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                                    @if($mentor->verification_document)
                                        <a href="{{ route('admin.mentor-verifications.document', $mentor) }}" target="_blank" class="rounded-lg border border-blue-200 px-4 py-2 font-semibold text-blue-700 hover:bg-blue-50">
                                            Voir le justificatif
                                        </a>
                                    @else
                                        <span class="rounded-lg border border-dashed border-slate-300 px-4 py-2 font-semibold text-slate-500">
                                            Aucun justificatif
                                        </span>
                                    @endif

                                    <a href="{{ route('web.mentors.show', $mentor) }}" target="_blank" class="rounded-lg border border-slate-200 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">
                                        Apercu profil
                                    </a>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.mentor-verifications.update', $mentor) }}" class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                @csrf
                                @method('PATCH')

                                <label class="block text-sm font-semibold text-slate-700" for="verification_status_{{ $mentor->id }}">Decision</label>
                                <select id="verification_status_{{ $mentor->id }}" name="verification_status" class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="verified">Valider</option>
                                    <option value="rejected">Refuser / demander correction</option>
                                </select>

                                <label class="mt-4 block text-sm font-semibold text-slate-700" for="verification_notes_{{ $mentor->id }}">Note admin</label>
                                <textarea id="verification_notes_{{ $mentor->id }}" name="verification_notes" rows="4" class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Expliquez le refus ou laissez une note interne.">{{ $mentor->verification_notes }}</textarea>

                                <button type="submit" class="mt-4 w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                                    Enregistrer la decision
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm text-slate-500">
                        Aucun conseiller dans cette categorie.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $mentors->links() }}
            </div>
        </main>
    </div>
</x-layouts.marketing>
