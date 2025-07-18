<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Message extends Model
{
    // make it each filed under each the previous one not in the same line 
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message',
        'is_read',
        'read_at'
    ];

    /**
     * Get the sender for the message.
     * return the sender that the message belongs to
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient for the message.
     * return the recipient that the message belongs to
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
