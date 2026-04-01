<x-layouts.marketing>
    <div class="min-h-screen bg-[#eef3f9]">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 rounded-3xl bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900">Messagerie</h1>
                        <p class="mt-2 text-sm text-slate-500">
                            Echangez avec votre mentor ou votre mentore, puis lancez un appel audio ou video depuis la conversation.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('sessions.index') }}" class="inline-flex rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Voir les sessions
                        </a>
                        <a href="{{ route('dashboard') }}" class="inline-flex rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                            Retour au dashboard
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[340px_minmax(0,1fr)]">
                <aside class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Conversations</h2>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse($conversations as $conversation)
                            @php
                                $peer = auth()->user()->role === 'mentor'
                                    ? $conversation->mentee?->user
                                    : $conversation->mentor?->user;
                            @endphp
                            <a href="{{ route('messages.show', $conversation) }}" class="block px-5 py-4 transition hover:bg-slate-50 {{ $activeConversation && $activeConversation->id === $conversation->id ? 'bg-blue-50' : '' }}">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-orange-100 text-sm font-bold text-orange-700">
                                        {{ strtoupper(substr($peer->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ $peer->name ?? 'Contact' }}</p>
                                        <p class="mt-1 truncate text-xs text-slate-500" data-conversation-preview="{{ $conversation->id }}">
                                            {{ $conversation->latestMessage?->body ?: 'Aucun message pour le moment.' }}
                                        </p>
                                        <p class="mt-2 text-[11px] font-medium text-slate-400" data-conversation-time="{{ $conversation->id }}">
                                            {{ $conversation->last_message_at?->diffForHumans() ?: 'Nouvelle conversation' }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-5 py-10 text-center text-sm text-slate-500">
                                Aucune conversation disponible pour le moment.
                            </div>
                        @endforelse
                    </div>
                </aside>

                <section
                    class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                    @if($activeConversation)
                        data-messages-root
                        data-conversation-id="{{ $activeConversation->id }}"
                        data-current-user-id="{{ auth()->id() }}"
                    @endif
                >
                    @if($activeConversation)
                        @php
                            $peer = auth()->user()->role === 'mentor'
                                ? $activeConversation->mentee?->user
                                : $activeConversation->mentor?->user;
                        @endphp
                        <div class="border-b border-slate-200 px-6 py-5">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 text-sm font-bold text-orange-700">
                                        {{ strtoupper(substr($peer->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-semibold text-slate-900">{{ $peer->name ?? 'Contact' }}</h2>
                                        <p class="text-sm text-slate-500">
                                            {{ $activeConversation->session?->title ?: 'Conversation privee' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('messages.call', ['conversation' => $activeConversation, 'mode' => 'audio']) }}" class="inline-flex rounded-xl border border-emerald-200 px-4 py-3 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                        Appel audio
                                    </a>
                                    <a href="{{ route('messages.call', ['conversation' => $activeConversation, 'mode' => 'video']) }}" class="inline-flex rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                                        Appel video
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="max-h-[540px] space-y-4 overflow-y-auto bg-slate-50 px-6 py-6" data-messages-list>
                            @forelse($activeConversation->messages as $message)
                                @php($mine = $message->sender_id === auth()->id())
                                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xl rounded-2xl px-4 py-3 shadow-sm {{ $mine ? 'bg-slate-900 text-white' : 'bg-white text-slate-800' }}">
                                        <p class="text-sm leading-6">{{ $message->body }}</p>
                                        <p class="mt-2 text-[11px] {{ $mine ? 'text-slate-300' : 'text-slate-400' }}">
                                            {{ $message->sender->name ?? 'Utilisateur' }} · {{ $message->created_at?->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500" data-empty-state>
                                    Commencez la conversation avec un premier message.
                                </div>
                            @endforelse
                        </div>

                        <div class="border-t border-slate-200 px-6 py-5">
                            @if($errors->any())
                                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('messages.store', $activeConversation) }}" class="space-y-4" data-message-form>
                                @csrf
                                <textarea name="body" rows="4" placeholder="Ecrivez votre message..." class="w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('body') }}</textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                                        Envoyer le message
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="flex min-h-[620px] items-center justify-center px-6 py-12">
                            <div class="max-w-md text-center">
                                <h2 class="text-2xl font-bold text-slate-900">Choisissez une conversation</h2>
                                <p class="mt-3 text-sm leading-6 text-slate-500">
                                    Vous pourrez ensuite envoyer des messages, relancer l'echange, ou lancer un appel audio ou video.
                                </p>
                            </div>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-layouts.marketing>
