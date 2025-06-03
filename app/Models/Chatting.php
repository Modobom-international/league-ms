<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chatting extends Model
{
    protected $fillable = ['conversation_id', 'user_id', 'content', 'file'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
