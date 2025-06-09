<?php

namespace App\Events;

use App\Models\Chatting;
use App\Models\Conversation;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Log;

class ChatSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $message;

    public function __construct(Conversation $conversation, Chatting $message)
    {
        $this->conversation = $conversation;
        $this->message = $message;

        // Load user nếu chưa có
        $this->message->loadMissing('user');

        Log::info('⚡ ChatSent event fired', [
            'conversation_id' => $conversation->id,
            'user_id' => $message->user_id,
            'content' => $message->content,
        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->conversation->id);
    }

    public function broadcastAs()
    {
        return 'ChatSent';
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'user_id' => $this->message->user_id,
                'user_name' => optional($this->message->user)->name,
                'user_avatar' => optional($this->message->user)->profile_photo_path
                    ? asset($this->message->user->profile_photo_path)
                    : asset('images/default-avatar.png'),
                'content' => $this->message->content,
                'file' => $this->message->file,
                'file_url' => $this->message->file ? asset($this->message->file) : null,
                'created_at' => $this->message->created_at->toDateTimeString(),
            ]
        ];
    }
}
