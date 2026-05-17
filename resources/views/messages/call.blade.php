<x-layouts.marketing>
    @php
        $jitsiBaseUrl = rtrim(config('services.jitsi.base_url', 'https://meet.jit.si'), '/');
        $jitsiOptions = [
            'config.prejoinPageEnabled=false',
            'config.disableDeepLinking=true',
            'config.startWithAudioMuted=false',
            'config.startWithVideoMuted='.($mode === 'audio' ? 'true' : 'false'),
        ];

        if ($mode === 'audio') {
            $jitsiOptions[] = 'config.startAudioOnly=true';
        }

        $jitsiUrl = $jitsiBaseUrl.'/'.rawurlencode($room).'#'.implode('&', $jitsiOptions);
    @endphp

    <div
        class="call-shell min-h-screen text-slate-50"
        data-call-root
        data-mode="{{ $mode }}"
        data-call-url="{{ $jitsiUrl }}"
    >
        <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-white/5 px-5 py-4 shadow-2xl backdrop-blur md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-cyan-200/80">
                        Salle privee
                    </p>
                    <h1 class="mt-2 text-2xl font-bold sm:text-3xl">
                        Appel {{ $mode === 'audio' ? 'audio' : 'video' }} avec {{ $peer->name ?? 'votre contact' }}
                    </h1>
                    <p class="mt-2 text-sm text-slate-200/80">
                        {{ $conversation->session?->title ?: 'Conversation directe' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('messages.show', $conversation) }}" class="inline-flex rounded-xl border border-white/15 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Retour a la conversation
                    </a>
                    <button type="button" class="inline-flex rounded-xl bg-rose-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-900/30 transition hover:bg-rose-400" data-end-call>
                        Terminer l'appel
                    </button>
                </div>
            </header>

            <main class="mt-6 grid flex-1 gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <section class="relative overflow-hidden rounded-2xl border border-white/10 bg-slate-950/70 shadow-[0_30px_120px_rgba(15,23,42,0.55)]">
                    <div class="call-backdrop absolute inset-0"></div>
                    <div class="relative flex min-h-[640px] flex-col">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 bg-slate-950/70 px-5 py-4">
                            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-300"></span>
                                Salle active
                            </div>
                            <p class="text-xs font-medium text-slate-300" data-call-status>
                                Chargement de l'appel...
                            </p>
                        </div>

                        <div class="relative flex-1 bg-slate-950">
                            <iframe
                                src="{{ $jitsiUrl }}"
                                title="Appel {{ $mode === 'audio' ? 'audio' : 'video' }} MentorConnect"
                                class="absolute inset-0 h-full w-full"
                                allow="camera; microphone; fullscreen; display-capture; autoplay; clipboard-write"
                                referrerpolicy="strict-origin-when-cross-origin"
                                data-call-frame
                            ></iframe>
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl backdrop-blur">
                        <p class="text-xs uppercase text-slate-300/70">Etat de l'appel</p>
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
                                <span>Mode</span>
                                <span class="font-semibold text-emerald-200">{{ $mode === 'audio' ? 'Audio' : 'Video' }}</span>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-white/10 bg-gradient-to-br from-cyan-400/15 via-blue-500/10 to-slate-900/30 p-6 shadow-xl">
                        <p class="text-xs uppercase text-cyan-100/80">Acces direct</p>
                        <p class="mt-4 text-sm leading-7 text-slate-100/90">
                            Si le navigateur bloque l'integration, ouvrez la salle dans un nouvel onglet.
                        </p>
                        <a href="{{ $jitsiUrl }}" target="_blank" rel="noopener" class="mt-5 inline-flex w-full justify-center rounded-xl bg-cyan-300 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">
                            Ouvrir l'appel
                        </a>
                    </section>
                </aside>
            </main>
        </div>
    </div>
</x-layouts.marketing>
