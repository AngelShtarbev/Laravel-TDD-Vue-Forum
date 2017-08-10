<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;
    private $thread;

    public function setUp()
    {
        parent::setUp();
        $this->thread = create('App\Thread');
    }

    /** @test  */
    public function user_can_read_all_threads()
    {
        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);

    }

    /** @test  */
    public function user_can_read_single_thread()
    {
        $response = $this->get($this->thread->path());
        $response->assertSee($this->thread->title);
    }

    /** @test  */
    public function user_can_read_replies_associated_with_thread()
    {
        $reply = factory('App\Reply')->create(['thread_id' => $this->thread->id]);
        $this->get($this->thread->path())->assertSee($reply->body);
    }

    /** @test  */
    public function user_can_filter_threads_by_tag()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/' . $channel->slug)->assertSee($threadInChannel->title)->assertDontSee($threadNotInChannel->title);
    }

    /** @test  */
    public function user_can_filter_threads_by_username()
    {
        $this->signIn(create('App\User', ['name' => 'Angel Shtarbev']));

        $myThread = create('App\Thread', ['user_id' => auth()->id()]);

        $otherThread = create('App\Thread');

        $this->get('threads?by=Angel Shtarbev')->assertSee($myThread->title)->assertDontSee($otherThread->title);
    }

    /** @test  */
    public function user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id],2);

        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id],3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response, 'replies_count'));
    }
}
