<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\TypingIndicator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Get all conversations for the authenticated user.
     */
    public function getConversations(Request $request)
    {
        $userId = Auth::id();
        
        $conversations = Conversation::forUser($userId)
            ->with(['userOne', 'userTwo', 'lastMessage.sender'])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($conversation) use ($userId) {
                $otherUser = $conversation->getOtherUser($userId);
                $unreadCount = $conversation->getUnreadCount($userId);
                $lastMessage = $conversation->lastMessage;

                return [
                    'id' => $conversation->id,
                    'user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'email' => $otherUser->email,
                        'avatar' => $otherUser->profile_photo_url,
                        'role' => $otherUser->role,
                        'is_online' => $otherUser->isOnline(),
                    ],
                    'last_message' => $lastMessage ? [
                        'id' => $lastMessage->id,
                        'message' => $lastMessage->message,
                        'sender_id' => $lastMessage->sender_id,
                        'is_read' => $lastMessage->is_read,
                        'created_at' => $lastMessage->created_at->toISOString(),
                    ] : null,
                    'unread_count' => $unreadCount,
                    'last_message_at' => $conversation->last_message_at?->toISOString(),
                ];
            });

        return response()->json([
            'conversations' => $conversations,
        ]);
    }

    /**
     * Search users for chat.
     */
    public function searchUsers(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $query = $request->input('query');
        $userId = Auth::id();

        $users = User::where('id', '!=', $userId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->profile_photo_url,
                    'role' => $user->role,
                    'is_online' => $user->isOnline(),
                ];
            });

        return response()->json([
            'users' => $users,
        ]);
    }

    /**
     * Start or get a conversation with a user.
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = Auth::id();
        $otherUserId = $request->input('user_id');

        if ($userId == $otherUserId) {
            return response()->json([
                'error' => 'Cannot start conversation with yourself',
            ], 400);
        }

        $conversation = Conversation::findOrCreateBetween($userId, $otherUserId);
        $otherUser = $conversation->getOtherUser($userId);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                    'avatar' => $otherUser->profile_photo_url,
                    'role' => $otherUser->role,
                    'is_online' => $otherUser->isOnline(),
                ],
            ],
        ]);
    }

    /**
     * Get messages for a conversation.
     */
    public function getMessages(Request $request, $conversationId)
    {
        $userId = Auth::id();
        
        $conversation = Conversation::forUser($userId)
            ->with(['userOne', 'userTwo'])
            ->findOrFail($conversationId);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'avatar' => $message->sender->profile_photo_url,
                    ],
                    'is_read' => $message->is_read,
                    'read_at' => $message->read_at?->toISOString(),
                    'created_at' => $message->created_at->toISOString(),
                ];
            });

        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $userId = Auth::id();
        
        $conversation = Conversation::forUser($userId)
            ->findOrFail($conversationId);

        $message = ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'message' => $request->input('message'),
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);

        // Stop typing indicator
        TypingIndicator::updateStatus($conversation->id, $userId, false);

        $message->load('sender');

        return response()->json([
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'avatar' => $message->sender->profile_photo_url,
                ],
                'is_read' => $message->is_read,
                'read_at' => $message->read_at?->toISOString(),
                'created_at' => $message->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Request $request, $conversationId)
    {
        $userId = Auth::id();
        
        $conversation = Conversation::forUser($userId)
            ->findOrFail($conversationId);

        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Update typing indicator.
     */
    public function updateTyping(Request $request, $conversationId)
    {
        $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        $userId = Auth::id();
        
        $conversation = Conversation::forUser($userId)
            ->findOrFail($conversationId);

        TypingIndicator::updateStatus(
            $conversation->id,
            $userId,
            $request->input('is_typing')
        );

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Get typing indicator status.
     */
    public function getTypingStatus(Request $request, $conversationId)
    {
        $userId = Auth::id();
        
        $conversation = Conversation::forUser($userId)
            ->findOrFail($conversationId);

        $otherUser = $conversation->getOtherUser($userId);

        $typingIndicator = TypingIndicator::where('conversation_id', $conversation->id)
            ->where('user_id', $otherUser->id)
            ->where('is_typing', true)
            ->where('last_typed_at', '>', now()->subSeconds(5))
            ->first();

        return response()->json([
            'is_typing' => $typingIndicator !== null,
        ]);
    }

    /**
     * Get unread messages count.
     */
    public function getUnreadCount(Request $request)
    {
        $userId = Auth::id();

        $unreadCount = ConversationMessage::whereHas('conversation', function ($query) use ($userId) {
            $query->forUser($userId);
        })
        ->where('sender_id', '!=', $userId)
        ->where('is_read', false)
        ->count();

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Delete a conversation.
     */
    public function deleteConversation(Request $request, $conversationId)
    {
        $userId = Auth::id();
        
        $conversation = Conversation::forUser($userId)
            ->findOrFail($conversationId);

        $conversation->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Set user online status.
     */
    public function setOnline(Request $request)
    {
        Auth::user()->setOnline();

        return response()->json([
            'success' => true,
        ]);
    }
}
