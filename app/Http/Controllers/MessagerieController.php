<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagerieController extends Controller
{
    /**
     * Données partagées par toutes les vues Messagerie.
     */
    private function sharedData(): array
    {
        return [
            'conversations' => Auth::user()
                ->conversations()
                ->with(['users', 'messages' => fn($q) => $q->latest()->limit(1)])
                ->latest()
                ->get()
                ->map(fn($conv) => [
                    'id'           => $conv->id,
                    'participants' => $conv->users->where('id', '!=', Auth::id())->values(),
                    'last_message' => $conv->messages->first(),
                    'updated_at'   => $conv->updated_at,
                ]),

            'users' => User::where('id', '!=', Auth::id())
                ->get(['id', 'name', 'email']),
        ];
    }

    /**
     * Liste des conversations.
     */
    public function index()
    {
        return inertia('Messagerie', $this->sharedData());
    }

    /**
     * Conversation ouverte avec ses messages.
     */
    public function show(Conversation $conversation)
    {
        abort_unless(
            $conversation->users()->where('user_id', Auth::id())->exists(),
            403
        );

        return inertia('Messagerie', array_merge($this->sharedData(), [
            'selectedConversation' => $conversation->id,
            'messages'             => $conversation->messages()
                ->with('user')
                ->oldest()
                ->get(),
        ]));
    }

    /**
     * Crée une nouvelle conversation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',  // ← retirer different: temporairement pour tester
        ]);

        $participantIds = collect($request->user_ids)
            ->push(Auth::id())
            ->unique()
            ->sort()
            ->values();

        $existing = Conversation::whereHas('users', function ($q) use ($participantIds) {
            $q->whereIn('user_id', $participantIds);
        })
            ->withCount('users')
            ->get()
            ->first(function ($conv) use ($participantIds) {
                return $conv->users->pluck('id')->sort()->values()->toArray() === $participantIds->toArray();
            });


        if ($existing) {
            return redirect()->route('messagerie.show', $existing);
        }

        $conversation = Conversation::create();
        $conversation->users()->attach($participantIds);

        return redirect()->route('messagerie.show', $conversation);
    }

    /**
     * Envoie un message (JSON pour Axios).
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        abort_unless(
            $conversation->users()->where('user_id', Auth::id())->exists(),
            403
        );

        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $message = $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        $conversation->touch();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message->load('user'),
            ], 201);
        }

        return redirect()->route('messagerie.show', $conversation);
    }

    /**
     * Supprime un message.
     */
    public function destroyMessage(Message $message)
    {
        abort_unless($message->user_id === Auth::id(), 403);

        $conversationId = $message->conversation_id;
        $message->delete();

        return redirect()->route('messagerie.show', $conversationId);
    }

    /**
     * Messages d'une conversation (JSON pour polling).
     */
    public function messages(Conversation $conversation)
    {
        abort_unless(
            $conversation->users()->where('user_id', Auth::id())->exists(),
            403
        );

        return response()->json([
            'messages' => $conversation->messages()->with('user')->oldest()->get(),
        ]);
    }
}
