<x-layouts.marketing>
    <section class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-4">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($mentor->user->name ?? 'Mentor') }}&background=2563eb&color=fff"
                            alt="{{ $mentor->user->name ?? 'Mentor' }}"
                            class="h-20 w-20 rounded-2xl object-cover"
                        >
                        <div>
                            <h1 class="text-3xl font-bold text-slate-900">{{ $mentor->user->name ?? 'Mentor' }}</h1>
                            <p class="mt-1 text-slate-500">{{ $mentor->expertise_title }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('web.mentors.index') }}"
                           class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Retour aux mentors
                        </a>

                        @auth
                            @if(auth()->user()->role === 'mentee')
                                <form method="POST" action="{{ route('messages.start.mentor', $mentor) }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                        Contacter ce mentor
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('sessions.index') }}"
                                   class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    Voir mes sessions
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Se connecter
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <h2 class="text-xl font-bold text-slate-900">A propos</h2>
                        <p class="mt-3 text-slate-600">
                            {{ $mentor->bio ?: 'Mentor experimente, pret a vous accompagner.' }}
                        </p>

                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-slate-900">Domaines</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @forelse($mentor->domains as $domain)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ $domain->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-slate-500">Aucun domaine renseigne.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <aside class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="text-lg font-semibold text-slate-900">Infos rapides</h3>
                        <div class="mt-4 space-y-3 text-sm text-slate-600">
                            <div class="flex items-center justify-between">
                                <span>Experience</span>
                                <span class="font-semibold text-slate-900">
                                    {{ $mentor->years_experience ?? 0 }} ans
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Tarif</span>
                                <span class="font-semibold text-slate-900">
                                    {{ $mentor->hourly_rate ? number_format($mentor->hourly_rate, 2) : 'Sur demande' }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-400">Disponibilite</p>
                                <p class="mt-1 text-slate-700">
                                    {{ $mentor->availability ?: 'A definir' }}
                                </p>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</x-layouts.marketing>
