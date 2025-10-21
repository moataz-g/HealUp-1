<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypingIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'is_typing',
        'last_typed_at',
    ];

    protected $casts = [
        'is_typing' => 'boolean',
        'last_typed_at' => 'datetime',
    ];

    /**
     * Get the conversation.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update typing status.
     */
    public static function updateStatus($conversationId, $userId, $isTyping = true)
    {
        return static::updateOrCreate(
            [
                'conversation_id' => $conversationId,
                'user_id' => $userId,
            ],
            [
                'is_typing' => $isTyping,
                'last_typed_at' => now(),
            ]
        );
    }
}
