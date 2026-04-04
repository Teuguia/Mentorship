<x-layouts.marketing>
    <div
        class="call-shell min-h-screen text-slate-50"
        data-call-root
        data-mode="{{ $mode }}"
    >
        <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-[32px] border border-white/10 bg-white/5 px-5 py-4 shadow-2xl backdrop-blur md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-200/80">
                        Salle privee
                    </p>
                    <h1 class="mt-2 text-2xl font-bold sm:text-3xl">
                        Appel {{ $mode === 'audio' ? 'audio' : 'video' }} avec {{ $peer->name ?? 'votre contact' }}
                    </h1>
                    <p class="mt-2 text-sm text-slate-200/80">
                        {{ $conversation->session?->title ?: 'Conversation directe' }} · {{ $room }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('messages.show', $conversation) }}" class="inline-flex rounded-2xl border border-white/15 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Retour a la conversation
                    </a>
                    <button type="button" class="inline-flex rounded-2xl bg-rose-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-900/30 transition hover:bg-rose-400" data-end-call>
                        Terminer l'appel
                    </button>
                </div>
            </header>

            <main class="mt-6 grid flex-1 gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                <section class="relative overflow-hidden rounded-[36px] border border-white/10 bg-slate-950/70 shadow-[0_30px_120px_rgba(15,23,42,0.55)]">
                    <div class="call-backdrop absolute inset-0"></div>
                    <div class="relative flex h-full min-h-[540px] flex-col justify-between p-6 sm:p-8">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="max-w-xl">
                                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-300 animate-pulse"></span>
                                    Appel sortant en cours
                                </div>
                                <h2 class="mt-5 text-4xl font-black tracking-tight text-white sm:text-5xl">
                                    {{ $peer->name ?? 'Contact' }}
                                </h2>
                                <p class="mt-3 max-w-lg text-sm leading-7 text-slate-200/80 sm:text-base">
                                    L'interface reste dans MentorConnect. Les pingouins defilent pendant la sonnerie pour rendre l'appel plus vivant.
                                </p>
                            </div>

                            <div class="rounded-[28px] border border-white/10 bg-white/5 px-5 py-4 text-right shadow-xl backdrop-blur">
                                <p class="text-xs uppercase tracking-[0.25em] text-slate-300/70">Mode</p>
                                <p class="mt-2 text-lg font-semibold text-white">
                                    {{ $mode === 'audio' ? 'Audio seulement' : 'Video active' }}
                                </p>
                            </div>
                        </div>

                        <div class="call-penguin-marquee mt-10">
                            <div class="call-penguin-track">
                                @foreach(range(1, 16) as $index)
                                    <div class="call-penguin">
                                        <span class="call-penguin-icon">🐧</span>
                                        <span class="call-penguin-ring"></span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-[1fr_280px]">
                            <div class="rounded-[32px] border border-white/10 bg-slate-900/55 p-5 shadow-2xl backdrop-blur">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.25em] text-slate-300/70">Participant distant</p>
                                        <p class="mt-2 text-xl font-semibold text-white">{{ $peer->name ?? 'Contact' }}</p>
                                    </div>
                                    <div class="rounded-full border border-cyan-300/20 bg-cyan-300/10 px-3 py-1 text-xs font-semibold text-cyan-100">
                                        En attente de connexion
                                    </div>
                                </div>

                                <div class="mt-6 flex min-h-[250px] items-center justify-center rounded-[28px] border border-dashed border-white/15 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950">
                                    <div class="text-center">
                                        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-cyan-300/10 text-4xl font-black text-cyan-100 shadow-[0_0_40px_rgba(103,232,249,0.25)]">
                                            {{ strtoupper(substr($peer->name ?? 'C', 0, 1)) }}
                                        </div>
                                        <p class="mt-5 text-lg font-semibold text-white">Connexion en cours...</p>
                                        <p class="mt-2 text-sm text-slate-300/80">
                                            Quand l'autre personne rejoint, son flux apparaitra ici.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <aside class="rounded-[32px] border border-white/10 bg-white/5 p-4 shadow-xl backdrop-blur">
                                <p class="text-xs uppercase tracking-[0.25em] text-slate-300/70">Apercu local</p>
                                <div class="relative mt-4 overflow-hidden rounded-[24px] border border-white/10 bg-slate-950">
                                    <video
                                        class="h-[260px] w-full bg-slate-950 object-cover {{ $mode === 'audio' ? 'hidden' : '' }}"
                                        autoplay
                                        muted
                                        playsinline
                                        data-local-video
                                    ></video>
                                    <div class="absolute inset-0 flex items-center justify-center bg-slate-950/60 text-center text-sm text-slate-200 {{ $mode === 'audio' ? '' : 'hidden' }}" data-audio-state>
                                        Mode audio actif. La camera reste coupee.
                                    </div>
                                </div>
                                <div class="mt-4 grid grid-cols-3 gap-3">
                                    <button type="button" class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-xs font-semibold text-white transition hover:bg-white/10" data-toggle-mic>
                                        Micro
                                    </button>
                                    <button type="button" class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-xs font-semibold text-white transition hover:bg-white/10 {{ $mode === 'audio' ? 'opacity-40 pointer-events-none' : '' }}" data-toggle-camera>
                                        Camera
                                    </button>
                                    <button type="button" class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-xs font-semibold text-white transition hover:bg-white/10" data-toggle-speaker>
                                        Haut-parleur
                                    </button>
                                </div>
                            </aside>
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-[32px] border border-white/10 bg-white/5 p-6 shadow-xl backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-300/70">Etat de l'appel</p>
                        <div class="mt-5 space-y-4 text-sm text-slate-200/85">
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <span>Conversation</span>
                                <span class="font-semibold text-white">{{ $conversation->id }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <span>Salle</span>
                                <span class="font-semibold text-white">{{ $room }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <span>Signal</span>
                                <span class="font-semibold text-emerald-200">Pret pour la demo</span>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[32px] border border-white/10 bg-gradient-to-br from-cyan-400/15 via-blue-500/10 to-slate-900/30 p-6 shadow-xl">
                        <p class="text-xs uppercase tracking-[0.25em] text-cyan-100/80">Scenario demo</p>
                        <ol class="mt-5 space-y-3 text-sm leading-7 text-slate-100/90">
                            <li>1. Ouvrir l'appel depuis la conversation du mentor.</li>
                            <li>2. Montrer l'animation de sonnerie et l'apercu local.</li>
                            <li>3. Faire rejoindre le mentoré pour illustrer le flux attendu.</li>
                        </ol>
                    </section>
                </aside>
            </main>
        </div>
    </div>
</x-layouts.marketing>
