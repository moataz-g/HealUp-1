<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withTimestamps()
            ->withPivot('registered_at');
    }
    protected $fillable = [
        'title',
        'date',
        'location',
        'description',
        'max_participants',
        'current_participants',
        'is_active',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
