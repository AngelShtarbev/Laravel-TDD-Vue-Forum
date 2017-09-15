<?php

namespace App\Events;
use Illuminate\Queue\SerializesModels;

class ThreadHasNewReply
{
    use SerializesModels;

    public $reply;

    /**
     * Create a new event instance.
     *
     * @param \App\Reply $reply
     */
    public function __construct($reply)
    {
        $this->reply = $reply;
    }


}
