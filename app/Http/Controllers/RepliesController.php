<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreatePostForm;
use App\Reply;
use App\Thread;
use Exception;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(5);
    }

    /**
     * @param $channelId
     * @param Thread $thread
     * @param CreatePostForm $form
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($channelId, Thread $thread, CreatePostForm $form)
    {
        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');

    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if(request()->expectsJson()) {
            return response(['status' => 'Reply successfully deleted !']);
        }

        return back();
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $this->validateReply();

        $reply->update(request(['body']));

        return $reply->load('owner');
    }

    protected function validateReply()
    {
        $this->validate(request(),['body' => 'required|blockspam']);
    }
}
