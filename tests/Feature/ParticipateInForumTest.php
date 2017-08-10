<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;
    private $thread;
    private $reply;

    public function setUp()
    {
        parent::setUp();
        $this->thread = create('App\Thread');
        $this->reply = make('App\Reply');
    }

    /** @test */
    public function unauthenticated_users_may_not_add_replies()
    {
       $this->post('/threads/some-channel/1/replies', [])->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_may_participate_in_forum_threads()
    {
       $this->signIn();

       $this->post($this->thread->path() . '/replies', $this->reply->toArray());

       // Then their reply should be visible on the page
       $this->get($this->thread->path())->assertSee($this->reply->body);
    }

    /** @test */
    public function reply_requires_body()
    {
        $this->signIn();

        $reply = make('App\Reply', ['body' => null]);

        $this->post($this->thread->path() . '/replies', $reply->toArray())->assertSessionHasErrors('body');
    }
}
