<?php

namespace App\Http\Controllers;

use App\Models\Advice;
use App\Models\ChatSession;
use Illuminate\Support\Facades\Auth;

class ChatSessionController extends Controller
{
    // Start a new session for a specific advice
    public function start(Advice $advice)
    {
        // Always create a new session instead of firstOrCreate
        $session = ChatSession::create([
            'advice_id' => $advice->id,
            'user_id' => Auth::id(),
            'status' => 'active',
            'started_at' => now(),
        ]);

        return redirect()->route('chat.sessions.show', $session->id);
    }

    // Show chat messages inside a session
    public function show($id)
    {
        $session = ChatSession::with(['messages', 'advice'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pages.chat-session', compact('session'));
    }

    public function destroy($id)
    {
        $session = ChatSession::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $session->delete();

        return redirect()->route('advices.index')
            ->with('success', 'Chat session deleted successfully.');
    }

}
