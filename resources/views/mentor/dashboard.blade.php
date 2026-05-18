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

            @if(session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="mb-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Profil public et verification</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Completez votre profil et ajoutez un CV, diplome ou certificat. Votre profil devient visible apres validation admin.
                            </p>
                        </div>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $mentor->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-700' : ($mentor->verification_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $mentor->verificationStatusLabel() }}
                        </span>
                    </div>
                </div>

                @if($mentor->verification_notes)
                    <div class="border-b border-amber-200 bg-amber-50 px-5 py-3 text-sm text-amber-800">
                        Note admin : {{ $mentor->verification_notes }}
                    </div>
                @endif

                <form method="POST" action="{{ route('mentor.profile.update') }}" enctype="multipart/form-data" class="grid gap-5 px-5 py-5 lg:grid-cols-2">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="expertise_title" class="block text-sm font-semibold text-slate-700">Titre d'expertise</label>
                        <input id="expertise_title" type="text" name="expertise_title" value="{{ old('expertise_title', $mentor->expertise_title) }}" class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="years_experience" class="block text-sm font-semibold text-slate-700">Annees d'experience</label>
                        <input id="years_experience" type="number" min="0" max="80" name="years_experience" value="{{ old('years_experience', $mentor->years_experience) }}" class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="hourly_rate" class="block text-sm font-semibold text-slate-700">Tarif horaire</label>
                        <input id="hourly_rate" type="number" min="0" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $mentor->hourly_rate) }}" class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="availability" class="block text-sm font-semibold text-slate-700">Disponibilite / lieu</label>
                        <input id="availability" type="text" name="availability" value="{{ old('availability', $mentor->availability) }}" class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="linkedin_url" class="block text-sm font-semibold text-slate-700">Lien LinkedIn</label>
                        <input id="linkedin_url" type="url" name="linkedin_url" value="{{ old('linkedin_url', $mentor->linkedin_url) }}" placeholder="https://www.linkedin.com/in/..." class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="portfolio_url" class="block text-sm font-semibold text-slate-700">Portfolio ou site</label>
                        <input id="portfolio_url" type="url" name="portfolio_url" value="{{ old('portfolio_url', $mentor->portfolio_url) }}" placeholder="https://..." class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="bio" class="block text-sm font-semibold text-slate-700">Bio professionnelle</label>
                        <textarea id="bio" name="bio" rows="4" class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('bio', $mentor->bio) }}</textarea>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="verification_document" class="block text-sm font-semibold text-slate-700">Justificatif</label>
                        <input id="verification_document" type="file" name="verification_document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700">
                        <p class="mt-2 text-xs text-slate-500">
                            Formats acceptes : PDF, image, DOC ou DOCX. Taille maximale : 5 Mo.
                            @if($mentor->verification_document_name)
                                Fichier actuel : {{ $mentor->verification_document_name }}.
                            @endif
                        </p>
                    </div>

                    <div class="lg:col-span-2">
                        <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                            Enregistrer et soumettre a verification
                        </button>
                    </div>
                </form>
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

            <section class="mt-6 grid gap-6 lg:grid-cols-2">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Creer une session de travail</h2>
                    <form method="POST" action="{{ route('dashboard.sessions.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <select name="contact_id" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Choisir un mentore</option>
                            @foreach($availableMentees as $mentee)
                                <option value="{{ $mentee->id }}">{{ $mentee->user->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Titre de la session" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <textarea name="description" rows="3" placeholder="Objectif de travail" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                            Planifier la session
                        </button>
                    </form>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Laisser un avis</h2>
                    <form method="POST" action="{{ route('dashboard.reviews.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <select name="session_id" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Choisir une session terminee</option>
                            @foreach($reviewableSessions as $session)
                                <option value="{{ $session->id }}">
                                    {{ $session->title }} - {{ $session->mentee->user->name ?? 'Mentore' }}
                                </option>
                            @endforeach
                        </select>
                        <select name="rating" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Note sur 5</option>
                            @foreach(range(1, 5) as $rating)
                                <option value="{{ $rating }}">{{ $rating }}/5</option>
                            @endforeach
                        </select>
                        <textarea name="comment" rows="3" placeholder="Votre avis sur la progression du mentore" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('comment') }}</textarea>
                        <button type="submit" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Enregistrer l'avis
                        </button>
                    </form>
                </section>
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
