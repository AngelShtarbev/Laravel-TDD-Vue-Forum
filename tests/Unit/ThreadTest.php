<?php

namespace Tests\Unit;

use App\Notifications\ThreadWasUpdated;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    private $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function thread_can_make_string_path()
    {
        $thread = create('App\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path());
    }

    /** @test */
    public function thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function thread_has_creator()
    {
       $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    /** @test */
    public function thread_add_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function check_if_the_authenticated_user_is_subscribed()
    {
        $thread = create('App\Thread');

        $this->signIn();

        $this->assertFalse($thread->isSubscribed);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribed);
    }

    /** @test */
    public function thread_belongs_to_channel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    /** @test */
    public function thread_can_be_subscribed()
    {
        // Given we have a thread
        $thread = create('App\Thread');

        // And an authenticated user
        $this->signIn();

        // When the user subscribes to the thread
        $thread->subscribe();

        // Then we should be able to fetch all threads that the user has subscribed to.
        $this->assertEquals(1,$thread->subscriptions()->where('user_id', auth()->id())->count());
    }

    /** @test */
    public function thread_can_be_unsubscribed_from()
    {
        // Given we have a thread
        $thread = create('App\Thread');

        // And a user who is subscribed to the thread
        $this->signIn();

        $thread->unsubscribe();

        $this->assertCount(0, $thread->subscriptions);
    }

    /** @test */
    public function thread_notifies_registered_subscribers_when_reply_is_added()
    {
        Notification::fake();

        $this->signIn();

        $this->thread->subscribe();

        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    public function thread_can_check_if_authenticated_user_read_all_replies()
    {
        $this->signIn();

        $this->assertTrue($this->thread->hasUpdatesFor(auth()->user()));

        auth()->user()->read($this->thread);

        $this->assertFalse($this->thread->hasUpdatesFor(auth()->user()));
    }
}
