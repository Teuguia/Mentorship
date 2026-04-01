<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Mentee;
use App\Models\Mentor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $conversations = $this->conversationsForUser($user);

        return view('messages.index', [
            'conversations' => $conversations,
            'activeConversation' => null,
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $user = Auth::user();
        $this->authorizeConversation($conversation, $user);

        $conversation->load([
            'mentor.user',
            'mentee.user',
            'messages.sender',
            'session',
        ]);

        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.index', [
            'conversations' => $this->conversationsForUser($user),
            'activeConversation' => $conversation,
        ]);
    }

    public function startWithMentor(Request $request, Mentor $mentor): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'mentee', 403);
        abort_unless(Auth::user()->mentee, 403, 'Profil mentee introuvable.');

        $conversation = Conversation::firstOrCreate(
            [
                'mentor_id' => $mentor->id,
                'mentee_id' => Auth::user()->mentee->id,
            ],
            [
                'call_room' => $this->generateRoomName($mentor->id, Auth::user()->mentee->id),
            ]
        );

        return $this->redirectAfterStart($request, $conversation);
    }

    public function startWithMentee(Request $request, Mentee $mentee): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'mentor', 403);
        abort_unless(Auth::user()->mentor, 403, 'Profil mentor introuvable.');

        $conversation = Conversation::firstOrCreate(
            [
                'mentor_id' => Auth::user()->mentor->id,
                'mentee_id' => $mentee->id,
            ],
            [
                'call_room' => $this->generateRoomName(Auth::user()->mentor->id, $mentee->id),
            ]
        );

        return $this->redirectAfterStart($request, $conversation);
    }

    public function call(Conversation $conversation, string $mode = 'video'): RedirectResponse
    {
        $this->authorizeConversation($conversation, Auth::user());

        if (! in_array($mode, ['audio', 'video'], true)) {
            $mode = 'video';
        }

        $room = ($conversation->call_room ?: $this->generateRoomName($conversation->mentor_id, $conversation->mentee_id)).'-'.$mode;

        $conversation->forceFill([
            'call_room' => $conversation->call_room ?: $this->generateRoomName($conversation->mentor_id, $conversation->mentee_id),
        ])->save();

        $baseUrl = rtrim(config('services.jitsi.base_url', 'https://meet.jit.si'), '/');

        return redirect()->away($baseUrl.'/'.$room);
    }

    protected function conversationsForUser($user)
    {
        return Conversation::query()
            ->with(['mentor.user', 'mentee.user', 'latestMessage.sender'])
            ->when($user->role === 'mentor', fn ($query) => $query->where('mentor_id', $user->mentor?->id))
            ->when($user->role === 'mentee', fn ($query) => $query->where('mentee_id', $user->mentee?->id))
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();
    }

    protected function authorizeConversation(Conversation $conversation, $user): void
    {
        $allowed = ($user->role === 'mentor' && $conversation->mentor_id === $user->mentor?->id)
            || ($user->role === 'mentee' && $conversation->mentee_id === $user->mentee?->id);

        abort_unless($allowed, 403);
    }

    protected function generateRoomName(int $mentorId, int $menteeId): string
    {
        return 'mentorconnect-'.Str::lower($mentorId.'-'.$menteeId.'-'.Str::random(8));
    }

    protected function redirectAfterStart(Request $request, Conversation $conversation): RedirectResponse
    {
        $mode = $request->string('mode')->toString();

        if (in_array($mode, ['audio', 'video'], true)) {
            return redirect()->route('messages.call', [
                'conversation' => $conversation,
                'mode' => $mode,
            ]);
        }

        return redirect()->route('messages.show', $conversation);
    }
}
