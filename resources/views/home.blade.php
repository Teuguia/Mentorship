<x-layouts.marketing>
    <header class="bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white font-bold">
                    M
                </div>
                <span class="text-xl font-bold text-blue-700">Mentorship</span>
            </a>

            <nav class="hidden items-center gap-8 md:flex">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600">Accueil</a>
                <a href="{{ route('web.mentors.index') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600">Mentors</a>
                <a href="{{ auth()->check() ? route('sessions.index') : route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600">Sessions</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                        Tableau de bord
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Inscription
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <section class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-500">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-12 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-16">
            <div class="text-white">
                <h1 class="text-4xl font-extrabold leading-tight sm:text-5xl">
                    Trouvez votre mentor idéal
                </h1>
                <p class="mt-4 max-w-xl text-lg text-blue-50">
                    Progressez grâce à l’accompagnement de mentors experts et accélérez votre développement.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('web.mentors.index') }}" class="rounded-lg bg-yellow-400 px-6 py-3 text-sm font-bold text-slate-900 hover:bg-yellow-300">
                        Trouver un mentor
                    </a>
                    <a href="{{ route('become.mentor') }}" class="rounded-lg border border-white bg-white/10 px-6 py-3 text-sm font-bold text-white hover:bg-white/20">
                        Devenir mentor
                    </a>
                </div>
            </div>

            <div class="rounded-2xl bg-white/10 p-4 backdrop-blur-sm">
                <img
                    src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=900&q=80"
                    alt="Mentor"
                    class="h-[320px] w-full rounded-2xl object-cover shadow-2xl"
                >
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-3xl font-bold text-slate-900">Rechercher un mentor par expertise</h2>

            <form action="{{ route('web.mentors.index') }}" method="GET" class="mt-6 grid gap-4 md:grid-cols-6">
                <select name="domain_id" class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Domaine</option>
                    @foreach($domains as $domain)
                        <option value="{{ $domain->id }}">{{ $domain->name }}</option>
                    @endforeach
                </select>

                <select name="location" class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Lieu</option>
                    <option value="Online">En ligne</option>
                    <option value="Yaoundé">Yaoundé</option>
                    <option value="Douala">Douala</option>
                    <option value="Remote">À distance</option>
                </select>

                <input
                    type="text"
                    name="q"
                    placeholder="Spécialité ou nom du mentor"
                    class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                >

                <input
                    type="number"
                    name="experience_min"
                    min="0"
                    step="1"
                    placeholder="Expérience min (années)"
                    class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                >

                <input
                    type="number"
                    name="rate_max"
                    min="0"
                    step="0.01"
                    placeholder="Tarif max"
                    class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                >

                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                    Rechercher
                </button>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-slate-900">Domaines populaires</h2>

        @php
            $domainIconMap = [
                'business' => '<svg viewBox="0 0 24 24" class="h-7 w-7 text-blue-700" fill="currentColor" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h4V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1h4a2 2 0 0 1 2 2v3H3V7zm0 5h20v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5z"/></svg>',
                'technology' => '<svg viewBox="0 0 24 24" class="h-7 w-7 text-blue-700" fill="currentColor" aria-hidden="true"><path d="M9 2a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2H9zm-4 4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5zm1 11a1 1 0 0 0-1 1v2h14v-2a1 1 0 0 0-1-1H6z"/></svg>',
                'marketing' => '<svg viewBox="0 0 24 24" class="h-7 w-7 text-blue-700" fill="currentColor" aria-hidden="true"><path d="M3 11a1 1 0 0 1 1-1h2.17l8.53-4.27A1 1 0 0 1 16 6.6v10.8a1 1 0 0 1-1.3.94L6.17 14H4a1 1 0 0 1-1-1v-2zm18 0a3 3 0 0 0-3-3v6a3 3 0 0 0 3-3z"/></svg>',
                'design' => '<svg viewBox="0 0 24 24" class="h-7 w-7 text-blue-700" fill="currentColor" aria-hidden="true"><path d="M4 17a3 3 0 1 0 3-3H4v3zm3-5a3 3 0 0 0 0-6H4v6h3zm5-6v6h3a3 3 0 1 0 0-6h-3zm0 8v6h3a3 3 0 1 0 0-6h-3z"/></svg>',
                'development' => '<svg viewBox="0 0 24 24" class="h-7 w-7 text-blue-700" fill="currentColor" aria-hidden="true"><path d="M8.7 5.3a1 1 0 0 1 0 1.4L5.4 10l3.3 3.3a1 1 0 1 1-1.4 1.4l-4-4a1 1 0 0 1 0-1.4l4-4a1 1 0 0 1 1.4 0zM15.3 5.3a1 1 0 0 0 0 1.4L18.6 10l-3.3 3.3a1 1 0 1 0 1.4 1.4l4-4a1 1 0 0 0 0-1.4l-4-4a1 1 0 0 0-1.4 0z"/></svg>',
                'default' => '<svg viewBox="0 0 24 24" class="h-7 w-7 text-blue-700" fill="currentColor" aria-hidden="true"><path d="M6 7a2 2 0 0 1 2-2h3V4a2 2 0 0 1 2-2h2v3h3a2 2 0 0 1 2 2v4H6V7zm0 6h16v5a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-5z"/></svg>',
            ];
        @endphp

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @forelse($domains as $domain)
                <a href="{{ route('web.mentors.index', ['domain_id' => $domain->id]) }}" class="rounded-2xl bg-white p-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    @php
                        $key = strtolower($domain->name);
                        $icon = $domainIconMap['default'];
                        foreach ($domainIconMap as $mapKey => $svg) {
                            if ($mapKey !== 'default' && str_contains($key, $mapKey)) {
                                $icon = $svg;
                                break;
                            }
                        }
                    @endphp
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                        {!! $icon !!}
                    </div>
                    <h3 class="mt-4 font-semibold text-slate-900">{{ $domain->name }}</h3>
                </a>
            @empty
                <div class="col-span-full rounded-2xl bg-white p-6 text-slate-500 shadow-sm">
                    Aucun domaine disponible pour le moment.
                </div>
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-slate-900">Témoignages</h2>
            <a href="{{ route('web.mentors.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Tout voir</a>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            @forelse($testimonials as $review)
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($review->mentor->user->name ?? 'Mentor') }}&background=2563eb&color=fff"
                            alt="Avatar"
                            class="h-16 w-16 rounded-xl object-cover"
                        >
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $review->mentor->expertise_title ?? 'Mentor' }}</h3>
                            <p class="text-sm text-slate-500">{{ $review->mentor->user->name ?? 'Mentor' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 text-yellow-500">
                        @for($i = 0; $i < $review->rating; $i++)
                            ★
                        @endfor
                    </div>

                    <p class="mt-3 text-slate-600">
                        {{ $review->comment ?: 'Excellente expérience de mentorat.' }}
                    </p>
                </div>
            @empty
                @foreach($featuredMentors as $mentor)
                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <img
                                src="https://ui-avatars.com/api/?name={{ urlencode($mentor->user->name ?? 'Mentor') }}&background=2563eb&color=fff"
                                alt="Avatar"
                                class="h-16 w-16 rounded-xl object-cover"
                            >
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $mentor->expertise_title }}</h3>
                                <p class="text-sm text-slate-500">{{ $mentor->user->name }}</p>
                            </div>
                        </div>

                        <div class="mt-4 text-yellow-500">★★★★★</div>
                        <p class="mt-3 text-slate-600">
                            {{ $mentor->bio ?: 'Mentor expérimenté, prêt à vous accompagner.' }}
                        </p>
                    </div>
                @endforeach
            @endforelse
        </div>
    </section>
</x-layouts.marketing>
