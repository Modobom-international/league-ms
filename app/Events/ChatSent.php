<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Log;

class ChatSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message, $conversation;

    public function __construct($conversation, $message)
    {
        $this->conversation = $conversation;
        $this->message = $message;
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

    public function broadcastWith()
    {
        return [
            'message' => [
                'user_id' => $this->message->user_id,
                'content' => $this->message->content,
                'file' => $this->message->file,
                'file_url' => $this->message->file ? asset($this->message->file) : null,
                'created_at' => $this->message->created_at->toDateTimeString(),
            ]
        ];
    }
    public function broadcastAs()
    {
        return 'ChatSent'; // Sẽ phải dùng `.listen('.ChatSent')` trong JS
    }




}
