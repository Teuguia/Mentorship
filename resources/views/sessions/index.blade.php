<x-layouts.marketing>
    <div class="min-h-screen bg-[#f4f7fb]">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900">Mes sessions</h1>
                        <p class="mt-2 text-sm text-slate-500">
                            Retrouvez vos rendez-vous, votre canal d'echange et les liens d'appel audio ou video.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('dashboard') }}" class="inline-flex rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Retour au dashboard
                        </a>
                        <a href="{{ route('messages.index') }}" class="inline-flex rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                            Ouvrir la messagerie
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-5">
                @forelse($sessions as $session)
                    @php
                        $contact = auth()->user()->role === 'mentor'
                            ? $session->mentee?->user
                            : $session->mentor?->user;
                    @endphp

                    <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-bold text-slate-900">{{ $session->title }}</h2>
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </div>

                                <div class="mt-3 grid gap-3 text-sm text-slate-600 md:grid-cols-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Interlocuteur</p>
                                        <p class="mt-1 font-medium text-slate-800">{{ $contact->name ?? 'Contact' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Date</p>
                                        <p class="mt-1 font-medium text-slate-800">{{ $session->scheduled_at?->format('d/m/Y \a\ H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Canal</p>
                                        <p class="mt-1 font-medium text-slate-800">{{ $session->meeting_link ? 'Lien de reunion disponible' : 'Appel via messagerie integree' }}</p>
                                    </div>
                                </div>

                                @if($session->description)
                                    <p class="mt-4 text-sm leading-6 text-slate-600">{{ $session->description }}</p>
                                @endif
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2 lg:w-[320px]">
                                @if($session->conversation)
                                    <a href="{{ route('messages.show', $session->conversation) }}" class="inline-flex items-center justify-center rounded-xl border border-blue-200 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                                        Voir les messages
                                    </a>
                                    <a href="{{ route('messages.call', ['conversation' => $session->conversation, 'mode' => 'audio']) }}" class="inline-flex items-center justify-center rounded-xl border border-emerald-200 px-4 py-3 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                        Appel audio
                                    </a>
                                    <a href="{{ route('messages.call', ['conversation' => $session->conversation, 'mode' => 'video']) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                                        Appel video
                                    </a>
                                @else
                                    @if(auth()->user()->role === 'mentor')
                                        <form method="POST" action="{{ route('messages.start.mentee', $session->mentee) }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-xl border border-blue-200 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                                                Demarrer la discussion
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('messages.start.mentee', $session->mentee) }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="audio">
                                            <button type="submit" class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                                Appel audio
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('messages.start.mentee', $session->mentee) }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="video">
                                            <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                                                Appel video
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('messages.start.mentor', $session->mentor) }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-xl border border-blue-200 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                                                Demarrer la discussion
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('messages.start.mentor', $session->mentor) }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="audio">
                                            <button type="submit" class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                                Appel audio
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('messages.start.mentor', $session->mentor) }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="video">
                                            <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                                                Appel video
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                @if($session->meeting_link)
                                    <a href="{{ $session->meeting_link }}" target="_blank" rel="noreferrer" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                        Ouvrir le lien de reunion
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-slate-500 shadow-sm">
                        Aucune session disponible pour le moment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.marketing>
