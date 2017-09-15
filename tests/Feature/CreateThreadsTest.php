<?php

namespace Tests\Feature;

use App\Activity;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadsTest extends TestCase
{
    //use DatabaseMigrations;

    /** @test*/
    public function guests_cannot_create_forum_threads()
    {
        $this->get('/threads/create')->assertRedirect('/login');

        $this->post(route('threads'))->assertRedirect('/login');
    }

    /** @test*/
    public function new_users_must_first_confirm_their_email_address()
    {
        $user = factory('App\User')->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = make('App\Thread');

        $this->post(route('threads'), $thread->toArray())->assertRedirect('/threads')->assertSessionHas('flash', 'You must first confirm your email address.');
    }

    /** @test*/
    public function authenticated_user_can_create_forum_threads()
    {
        $this->signIn();

        $thread = make('App\Thread');

        $response = $this->post(route('threads'), $thread->toArray());

        $this->get($response->headers->get('Location'))->assertSee($thread->title)->assertSee($thread->body);
    }

    /** @test*/
    public function thread_requires_title()
    {
        $this->publishThread(['title' => null])->assertSessionHasErrors('title');
    }

    /** @test*/
    public function thread_requires_body()
    {
        $this->publishThread(['body' => null])->assertSessionHasErrors('body');
    }

    /** @test*/
    public function thread_requires_valid_channel()
    {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])->assertSessionHasErrors('channel_id');

    }

    /** @test*/
    public function unauthorized_users_can_not_delete_threads()
    {
        $thread = create('App\Thread');

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();

        $this->delete($thread->path())->assertStatus(403);
    }

    /** @test*/
    public function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, Activity::count());
    }

    /** @test*/
    public function thread_requires_unique_slug()
    {
        $this->signIn();

        create('App\Thread', [], 2);

        $thread = create('App\Thread', ['title' => 'Foo Title']);

        $this->assertEquals($thread->fresh()->slug, 'foo-title');

        $thread = $this->postJson(route('threads'), $thread->toArray())->json();

        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);

    }

    /** @test */
    public function thread_with_title_ending_in_number_should_generate_the_proper_slug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Some Title 24']);

        $this->post(route('threads'), $thread->toArray());

        $thread = $this->postJson(route('threads'), $thread->toArray())->json();

        $this->assertTrue(Thread::whereSlug('foo-title-3')->exists());

        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);
     }

    public function publishThread($overrides = [])
    {

        $this->signIn();

        $thread = make('App\Thread', $overrides);

        return $this->post(route('threads'), $thread->toArray());
    }
}
