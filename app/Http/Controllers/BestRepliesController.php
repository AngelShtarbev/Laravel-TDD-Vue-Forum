<?php

namespace App\Http\Controllers;

use App\Reply;

class BestRepliesController extends Controller
{
    public function store(Reply $reply)
    {
       $this->authorize('update', $reply->thread);

       $reply->thread->update(['best_reply_id' => $reply->id]);

       $reply->thread->markBestReply($reply);
    }
}
