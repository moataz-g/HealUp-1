<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $session = ChatSession::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender' => 'user',
            'content' => $request->content,
            'sent_at' => now(),
        ]);

        return redirect()->route('chat.sessions.show', $session->id);
    }
}
