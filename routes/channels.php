<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversations.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\Conversation::query()->find($conversationId);

    if (! $conversation) {
        return false;
    }

    return ($user->role === 'mentor' && $conversation->mentor_id === $user->mentor?->id)
        || ($user->role === 'mentee' && $conversation->mentee_id === $user->mentee?->id);
});
