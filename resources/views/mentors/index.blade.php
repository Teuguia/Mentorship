<x-layouts.marketing>
    <section class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900">Trouver un mentor</h1>
                        <p class="mt-2 text-slate-500">
                            Filtrez par domaine, lieu, experience ou tarif.
                        </p>
                    </div>
                    <a href="{{ route('become.mentor') }}"
                       class="inline-flex rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                        Devenir mentor
                    </a>
                </div>

                <form action="{{ route('web.mentors.index') }}" method="GET" class="mt-6 grid gap-4 md:grid-cols-6">
                    <select name="domain_id" class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Domaine</option>
                        @foreach($domains as $domain)
                            <option value="{{ $domain->id }}" @selected((string) $domain->id === request('domain_id'))>
                                {{ $domain->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="location" class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Lieu</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" @selected(request('location') === $location)>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>

                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Specialite ou nom du mentor"
                        class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                    >

                    <input
                        type="number"
                        name="experience_min"
                        value="{{ request('experience_min') }}"
                        min="0"
                        step="1"
                        placeholder="Experience min (annees)"
                        class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                    >

                    <div class="grid grid-cols-2 gap-3">
                        <input
                            type="number"
                            name="rate_min"
                            value="{{ request('rate_min') }}"
                            min="0"
                            step="0.01"
                            placeholder="Tarif min"
                            class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                        >
                        <input
                            type="number"
                            name="rate_max"
                            value="{{ request('rate_max') }}"
                            min="0"
                            step="0.01"
                            placeholder="Tarif max"
                            class="rounded-lg border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                        Rechercher
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($mentors as $mentor)
                <div class="rounded-3xl bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($mentor->user->name ?? 'Mentor') }}&background=2563eb&color=fff"
                            alt="{{ $mentor->user->name ?? 'Mentor' }}"
                            class="h-16 w-16 rounded-2xl object-cover"
                        >
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">{{ $mentor->user->name ?? 'Mentor' }}</h3>
                            <p class="text-sm text-slate-500">{{ $mentor->expertise_title }}</p>
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-slate-600">
                        {{ $mentor->bio ?: 'Mentor experimente, pret a vous accompagner.' }}
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($mentor->domains as $domain)
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ $domain->name }}
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500">
                            {{ $mentor->availability ?: 'Disponibilites a definir' }}
                        </span>
                        <a href="{{ route('web.mentors.show', $mentor) }}"
                           class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                            Voir le profil
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-3xl bg-white p-8 text-center text-slate-500 shadow-sm">
                    Pas de mentor disponible.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $mentors->links() }}
        </div>
    </section>
</x-layouts.marketing>
