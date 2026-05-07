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
     * Affiche la liste des conversations de l'utilisateur connecté.
     */
    public function index()
{
    $conversations = Auth::user()
        ->conversations()
        ->with(['users', 'messages' => fn($q) => $q->latest()->limit(1)])
        ->withCount('messages')
        ->latest()
        ->get()
        ->map(function ($conversation) {
            return [
                'id'           => $conversation->id,
                'participants' => $conversation->users->where('id', '!=', Auth::id())->values(),
                'last_message' => $conversation->messages->first(),
                'updated_at'   => $conversation->updated_at,
            ];
        });

    $users = User::where('id', '!=', Auth::id())->get(['id', 'name', 'email']);

    return inertia('Messagerie', [
        'conversations' => $conversations,
        'users' => $users,
    ]);
}


    /**
     * Affiche une conversation et ses messages.
     */
    public function show(Conversation $conversation)
{
    abort_unless(
        $conversation->users()->where('user_id', Auth::id())->exists(),
        403
    );

    $messages = $conversation->messages()->with('user')->oldest()->get();

    return inertia('Messagerie', [
        'selectedConversation' => $conversation->id,
        'messages' => $messages,
        'conversations' => Auth::user()->conversations()
            ->with(['users', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->get()
            ->map(fn($conv) => [
                'id' => $conv->id,
                'participants' => $conv->users->where('id', '!=', Auth::id())->values(),
                'last_message' => $conv->messages->first(),
            ]),
    ]);
}


    /**
     * Crée une nouvelle conversation avec un ou plusieurs utilisateurs.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id|different:' . Auth::id(),
        ]);

        $participantIds = collect($request->user_ids)->push(Auth::id())->sort()->values();

        // Cherche une conversation existante avec exactement ces participants
        $existing = Conversation::whereHas('users', fn($q) => $q->whereIn('user_id', $participantIds))
            ->withCount('users')
            ->having('users_count', count($participantIds))
            ->first();

        if ($existing) {
            return redirect()->route('messagerie.show', $existing);
        }

        $conversation = Conversation::create();
        $conversation->users()->attach($participantIds);

        return redirect()->route('messagerie.show', $conversation);
    }

    /**
     * Envoie un message dans une conversation.
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
            ]);
        }

        return redirect()->route('messagerie.show', $conversation);
    }

    /**
     * Supprime un message (seulement si l'utilisateur en est l'auteur).
     */
    public function destroyMessage(Message $message)
    {
        abort_unless($message->user_id === Auth::id(), 403);

        $conversationId = $message->conversation_id;
        $message->delete();

        return redirect()->route('messagerie.show', $conversationId);
    }

    /**
     * Retourne les messages d'une conversation (API JSON pour polling/refresh).
     */
    public function messages(Conversation $conversation)
    {
        abort_unless(
            $conversation->users()->where('user_id', Auth::id())->exists(),
            403
        );

        $messages = $conversation->messages()->with('user')->oldest()->get();

        return response()->json(['messages' => $messages]);
    }
}