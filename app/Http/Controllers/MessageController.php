<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        $allowed = ($user->role === 'mentor' && $conversation->mentor_id === $user->mentor?->id)
            || ($user->role === 'mentee' && $conversation->mentee_id === $user->mentee?->id);

        abort_unless($allowed, 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $validated['body'],
        ]);

        $message->load('sender');

        $conversation->forceFill([
            'last_message_at' => now(),
        ])->save();

        broadcast(new MessageSent($message))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender?->name ?? 'Utilisateur',
                    'body' => $message->body,
                    'created_at' => $message->created_at?->format('d/m/Y H:i'),
                ],
            ]);
        }

        return redirect()->route('messages.show', $conversation);
    }
}
