<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the first user in the conversation.
     */
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    /**
     * Get the second user in the conversation.
     */
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    /**
     * Get all messages for the conversation.
     */
    public function messages()
    {
        return $this->hasMany(ConversationMessage::class);
    }

    /**
     * Get typing indicators for this conversation.
     */
    public function typingIndicators()
    {
        return $this->hasMany(TypingIndicator::class);
    }

    /**
     * Get the other participant in the conversation.
     */
    public function getOtherUser($userId)
    {
        return $this->user_one_id === $userId ? $this->userTwo : $this->userOne;
    }

    /**
     * Get unread messages count for a specific user.
     */
    public function getUnreadCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get the last message.
     */
    public function lastMessage()
    {
        return $this->hasOne(ConversationMessage::class)->latestOfMany();
    }

    /**
     * Scope to get conversations for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId);
    }

    /**
     * Find or create a conversation between two users.
     */
    public static function findOrCreateBetween($userOneId, $userTwoId)
    {
        // Ensure consistent ordering
        [$userId1, $userId2] = [$userOneId, $userTwoId];
        if ($userId1 > $userId2) {
            [$userId1, $userId2] = [$userId2, $userId1];
        }

        return static::firstOrCreate([
            'user_one_id' => $userId1,
            'user_two_id' => $userId2,
        ]);
    }
}
