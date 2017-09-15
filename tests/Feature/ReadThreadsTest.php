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

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /** @test  */
    public function user_can_request_all_replies_for_given_thread()
    {
        $thread = create('App\Thread');

        create('App\Reply', ['thread_id' => $thread->id],2);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2, $response['data']);

        $this->assertEquals(2, $response['total']);
    }

    /** @test  */
    public function user_can_filter_unanswered_threads()
    {
        $thread = create('App\Thread');

        create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1, $response['data']);
    }

    /** @test  */
    public function record_visits_each_time_thread_is_read()
    {
        $thread = create('App\Thread');

        $this->assertSame(0, $thread->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }

}
