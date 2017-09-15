<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadsTest extends TestCase {

    use DatabaseMigrations;

    /** @test */
    public function user_can_subscribe_to_threads()
    {
        // User must be signed in
        $this->signIn();

        // If there is a created thread
        $thread = create('App\Thread');

        // And the user subscribes to the thread
        $this->post($thread->path() . '/subscriptions');

        $this->assertCount(1, $thread->fresh()->subscriptions);

    }

    /** @test */
    public function user_can_unsubscribe_threads()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $thread->subscribe();

        $this->delete($thread->path() . '/subscriptions');

        $this->assertCount(0, $thread->subscriptions);
    }

}