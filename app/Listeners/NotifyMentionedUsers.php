<?php

namespace App\Listeners;
use App\Events\ThreadHasNewReply;
use App\Notifications\Mentioned;
use App\User;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadHasNewReply  $event
     * @return void
     */
    public function handle(ThreadHasNewReply $event)
    {
        User::whereIn('name', $event->reply->mentionedUsers())->get()->each(function ($user) use ($event) {
            $user->notify(new Mentioned($event->reply));
        });
    }
}
