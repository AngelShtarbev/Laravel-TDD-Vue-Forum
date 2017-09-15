<?php

namespace Tests\Feature;

use Exception;
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

       $this->assertDatabaseHas('replies',['body' => $this->reply->body]);

       $this->assertEquals(1, $this->thread->fresh()->replies_count);
    }

    /** @test */
    public function reply_requires_body()
    {
        $this->signIn();

        $reply = make('App\Reply', ['body' => null]);

        $this->post($this->thread->path() . '/replies', $reply->toArray())->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthorized_users_cannot_delete_replies()
    {
        $reply = create('App\Reply');

        $this->delete("/replies/{$reply->id}")->assertRedirect('login');

        $this->signIn();

        $this->delete("/replies/{$reply->id}")->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies',['id' => $reply->id]);

        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->patch("/replies/{$reply->id}", ['body' => 'Update request has been sent']);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => 'Update request has been sent']);
    }

    /** @test */
    public function unauthorized_users_cannot_update_replies()
    {
        $reply = create('App\Reply');

        $this->patch("/replies/{$reply->id}")->assertRedirect('login');

        $this->signIn();

        $this->patch("/replies/{$reply->id}")->assertStatus(403);
    }

    /** @test */
    public function deny_spam_replies()
    {
        $this->signIn();

        $reply = make('App\Reply', [
            'body' => 'Yahoo Customer Support'
        ]);

        $this->json('post',$this->thread->path() . '/replies', $reply->toArray())->assertStatus(422);
    }

    /** @test */
    public function users_can_reply_only_once_per_minute()
    {
        $this->signIn();

        $reply = make('App\Reply', [
            'body' => 'Some reply here.'
        ]);

        $this->post($this->thread->path() . '/replies', $reply->toArray())->assertStatus(200);

        $this->post($this->thread->path() . '/replies', $reply->toArray())->assertStatus(429);
    }
}
