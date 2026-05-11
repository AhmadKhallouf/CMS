<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender', 'receiver');
    }

    public function broadcastOn(): array
{
    return [
        new \Illuminate\Broadcasting\PrivateChannel('chat.' . $this->message->receiver_id),
        new \Illuminate\Broadcasting\PrivateChannel('chat.' . $this->message->sender_id)
    ];
}

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'sender_name' => $this->message->sender->name,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'is_read' => $this->message->is_read,
        ];
    }
    
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}