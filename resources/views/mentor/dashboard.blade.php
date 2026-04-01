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
                    <a href="{{ route('mentor.dashboard') }}" class="text-white">Tableau de bord</a>
                    <a href="{{ route('sessions.index') }}" class="transition hover:text-white">Sessions</a>
                    <a href="{{ route('messages.index') }}" class="transition hover:text-white">Messages</a>
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
                    Voici un aper&ccedil;u de vos activit&eacute;s r&eacute;centes.
                </p>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-medium text-blue-100">
                        <span class="rounded bg-white/15 px-1.5 py-0.5 text-xs">CAL</span>
                        <span>Sessions &agrave; venir</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold">{{ $stats['upcoming_sessions'] }}</p>
                    <a href="{{ route('sessions.index') }}" class="mt-4 inline-flex text-xs font-medium text-blue-100 hover:text-white">
                        Voir les sessions &rarr;
                    </a>
                </div>

                <div class="rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-medium text-emerald-50">
                        <span class="rounded bg-white/15 px-1.5 py-0.5 text-xs">USR</span>
                        <span>Mentor&eacute;s actifs</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold">{{ $stats['active_mentees'] }}</p>
                    <a href="#mentees" class="mt-4 inline-flex text-xs font-medium text-emerald-50 hover:text-white">
                        Voir les mentor&eacute;s &rarr;
                    </a>
                </div>

                <div class="rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 p-5 text-white shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-medium text-violet-100">
                        <span class="rounded bg-white/15 px-1.5 py-0.5 text-xs">HIS</span>
                        <span>Sessions termin&eacute;es</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold">{{ $stats['completed_sessions'] }}</p>
                    <a href="{{ route('sessions.index') }}" class="mt-4 inline-flex text-xs font-medium text-violet-100 hover:text-white">
                        Voir l'historique &rarr;
                    </a>
                </div>

                <div class="rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 p-5 text-white shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-medium text-orange-50">
                        <span class="rounded bg-white/15 px-1.5 py-0.5 text-xs">REV</span>
                        <span>&Eacute;valuations re&ccedil;ues</span>
                    </div>
                    <p class="mt-4 text-4xl font-bold">{{ $stats['reviews_count'] }}</p>
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
                                            Avec {{ $session->mentee->user->name ?? 'Mentore' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $session->scheduled_at?->format('d/m/Y \a\ H:i') }}
                                        </p>
                                    </div>
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-700">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <form method="POST" action="{{ route('messages.start.mentee', $session->mentee) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex rounded-md border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50">
                                            Envoyer un message
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="px-5 py-10 text-center text-sm text-slate-500">
                                Aucune session &agrave; venir pour le moment.
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
                        <h2 class="text-base font-semibold text-slate-900">Messages R&eacute;cents</h2>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse($recentConversations as $conversation)
                            <article class="flex items-start gap-3 px-5 py-4">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-700">
                                    {{ strtoupper(substr($conversation->mentee->user->name ?? 'M', 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $conversation->mentee->user->name ?? 'Mentore' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $conversation->latestMessage?->body ?: 'Aucun message envoy&eacute; pour le moment.' }}
                                    </p>
                                </div>
                                <a href="{{ route('messages.show', $conversation) }}" class="shrink-0 text-xs font-semibold text-blue-600 hover:text-blue-700">
                                    Ouvrir
                                </a>
                            </article>
                        @empty
                            <div class="px-5 py-10 text-center text-sm text-slate-500">
                                Aucun message r&eacute;cent pour le moment.
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

            <section id="mentees" class="mt-6">
                <div class="mb-4">
                    <h2 class="text-base font-semibold text-slate-900">Mes Mentor&eacute;s Actifs</h2>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($activeMentees as $mentee)
                        <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-100 text-lg font-bold text-orange-700">
                                    {{ strtoupper(substr($mentee->user->name ?? 'M', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-sm font-semibold text-slate-900">
                                        {{ $mentee->user->name ?? 'Mentore' }}
                                    </h3>
                                    <p class="truncate text-xs text-slate-500">
                                        {{ $mentee->profession ?: ($mentee->level ?: 'Parcours en cours de d&eacute;finition') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <form method="POST" action="{{ route('messages.start.mentee', $mentee) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex rounded-md border border-blue-200 px-4 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50">
                                        Envoyer un message
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-lg border border-dashed border-slate-300 bg-white px-5 py-10 text-center text-sm text-slate-500 md:col-span-2 xl:col-span-3">
                            Aucun mentor&eacute; actif n'est encore associ&eacute; &agrave; votre espace.
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</x-layouts.marketing>
