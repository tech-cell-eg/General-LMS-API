<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        Log::info('NewMessage event fired', [
            'sender' => $message->sender_id,
            'recipient' => $message->recipient_id,
            'channel' => 'user.' . $message->recipient_id
        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->message->recipient_id);
    }

    public function broadcastAs()
    {
        return 'message.new';
    }
    public function broadcastWith()
    {
        Log::info('Broadcasting message', [
            'message_id' => $this->message->id,
            'content' => $this->message->message,
            'recipient_channel' => 'user.' . $this->message->recipient_id
        ]);

        return [
            'message' => $this->message->load(['sender', 'recipient'])
        ];
    }
}
