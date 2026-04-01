<x-layouts.marketing>
    <div class="min-h-screen bg-[#f2f5fb] text-slate-900">
        <header class="border-b border-slate-700 bg-slate-800 text-white shadow-sm">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-white/10 ring-1 ring-white/15">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 text-sky-400" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 16.5L10.2 7.5C10.6 6.8 11.6 6.8 12 7.5L15 12.5L18.2 7.2C18.6 6.6 19.6 6.6 20 7.2L14.4 17.1C14 17.8 13.1 17.8 12.7 17.2L9.7 12.4L6.8 17.1C6.4 17.7 5.4 17.2 5 16.5Z" fill="currentColor" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold tracking-wide text-white/95">MentorConnect</span>
                </a>

                <nav class="hidden items-center gap-8 text-xs font-medium text-slate-200 md:flex">
                    <a href="{{ route('mentee.dashboard') }}" class="text-white">Tableau de bord</a>
                    <a href="{{ route('sessions.index') }}" class="transition hover:text-white">Sessions</a>
                    <a href="{{ route('messages.index') }}" class="transition hover:text-white">Messages</a>
                    <a href="{{ route('mentors.index') }}" class="transition hover:text-white">Mentors</a>
                </nav>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 transition hover:bg-white/10">
                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-orange-200 text-xs font-bold text-slate-700">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden text-xs font-medium text-slate-100 sm:inline">{{ auth()->user()->name }}</span>
                </a>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
            <section class="mb-6">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Bienvenue, {{ auth()->user()->name }} !
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Suivez vos rendez-vous, vos echanges et vos mentors depuis un seul espace.
                </p>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-medium text-blue-100">
                        <span class="rounded bg-white/15 px-1.5 py-0.5 text-xs">CAL</span>
                        <span>Sessions a venir</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold">{{ $stats['upcoming_sessions'] }}</p>
                    <a href="{{ route('sessions.index') }}" class="mt-4 inline-flex text-xs font-medium text-blue-100 hover:text-white">
                        Voir mes sessions &rarr;
                    </a>
                </div>

                <div class="rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-medium text-emerald-50">
                        <span class="rounded bg-white/15 px-1.5 py-0.5 text-xs">MTR</span>
                        <span>Mentors actifs</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold">{{ $stats['active_mentors'] }}</p>
                    <a href="{{ route('mentors.index') }}" class="mt-4 inline-flex text-xs font-medium text-emerald-50 hover:text-white">
                        Explorer les mentors &rarr;
                    </a>
                </div>

                <div class="rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 p-5 text-white shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-medium text-violet-100">
                        <span class="rounded bg-white/15 px-1.5 py-0.5 text-xs">HIS</span>
                        <span>Sessions terminees</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold">{{ $stats['completed_sessions'] }}</p>
                    <a href="{{ route('sessions.index') }}" class="mt-4 inline-flex text-xs font-medium text-violet-100 hover:text-white">
                        Voir l'historique &rarr;
                    </a>
                </div>

                <div class="rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 p-5 text-white shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-medium text-orange-50">
                        <span class="rounded bg-white/15 px-1.5 py-0.5 text-xs">MSG</span>
                        <span>Conversations</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold">{{ $stats['conversations_count'] }}</p>
                    <a href="{{ route('messages.index') }}" class="mt-4 inline-flex text-xs font-medium text-orange-50 hover:text-white">
                        Ouvrir la messagerie &rarr;
                    </a>
                </div>
            </section>

            <section class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Prochaines Sessions</h2>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse($upcomingSessions as $session)
                            <article class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-800">{{ $session->title }}</p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            Avec {{ $session->mentor->user->name ?? 'Mentor' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $session->scheduled_at?->format('d/m/Y \a\ H:i') }}
                                        </p>
                                    </div>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-3">
                                    <form method="POST" action="{{ route('messages.start.mentor', $session->mentor) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex rounded-md border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50">
                                            Envoyer un message
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('messages.start.mentor', $session->mentor) }}">
                                        @csrf
                                        <input type="hidden" name="mode" value="video">
                                        <button type="submit" class="inline-flex rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                            Rejoindre l'appel
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="px-5 py-10 text-center text-sm text-slate-500">
                                Aucune session a venir pour le moment.
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-slate-200 px-5 py-3 text-center">
                        <a href="{{ route('sessions.index') }}" class="inline-flex rounded-md border border-blue-200 px-4 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50">
                            Voir toutes les sessions
                        </a>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Messages Recents</h2>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse($recentConversations as $conversation)
                            <article class="flex items-start gap-3 px-5 py-4">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-700">
                                    {{ strtoupper(substr($conversation->mentor->user->name ?? 'M', 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $conversation->mentor->user->name ?? 'Mentor' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $conversation->latestMessage?->body ?: 'Aucun message envoye pour le moment.' }}
                                    </p>
                                </div>
                                <a href="{{ route('messages.show', $conversation) }}" class="shrink-0 text-xs font-semibold text-blue-600 hover:text-blue-700">
                                    Ouvrir
                                </a>
                            </article>
                        @empty
                            <div class="px-5 py-10 text-center text-sm text-slate-500">
                                Aucun message recent pour le moment.
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-slate-200 px-5 py-3 text-center">
                        <a href="{{ route('messages.index') }}" class="inline-flex rounded-md border border-blue-200 px-4 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50">
                            Voir tous les messages
                        </a>
                    </div>
                </div>
            </section>

            <section class="mt-6 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-slate-900">Avis Recents</h2>
                        <a href="{{ route('sessions.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                            Voir mes sessions
                        </a>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse($recentReviews as $review)
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $review->mentor->user->name ?? 'Mentor' }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $review->session->title ?? 'Session' }}</p>
                                    </div>
                                    <div class="text-amber-500">{{ str_repeat('*', (int) $review->rating) }}</div>
                                </div>
                                <p class="mt-3 text-sm text-slate-600">{{ $review->comment ?: 'Tres bonne session.' }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-slate-500">
                                Aucun avis recent pour le moment.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-6">
                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-slate-900">Mes domaines</h2>
                        <div class="mt-5 flex flex-wrap gap-3">
                            @forelse($domains as $domain)
                                <span class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">
                                    {{ $domain->name }}
                                </span>
                            @empty
                                <p class="text-sm text-slate-500">Aucun domaine selectionne pour le moment.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-slate-900">Actions rapides</h2>
                        <div class="mt-5 space-y-3">
                            <a href="{{ route('mentors.index') }}" class="flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                                Trouver un mentor
                            </a>
                            <a href="{{ route('messages.index') }}" class="flex w-full items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Ouvrir ma messagerie
                            </a>
                            <a href="{{ route('sessions.index') }}" class="flex w-full items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Voir mes sessions
                            </a>
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </div>
</x-layouts.marketing>
