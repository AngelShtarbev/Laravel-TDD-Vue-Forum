<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

   /** @test */
   public function notify_mentioned_users_in_reply()
   {
       $angel = create('App\User',['name' => 'Angel']);

       $jane = create('App\User',['name' => 'JaneDoe']);

       $this->signIn($angel);

       $thread = create('App\Thread');

       $reply = make('App\Reply', [
           'body' => '@JaneDoe check this one.'
       ]);

       $this->json('post',$thread->path() . '/replies', $reply->toArray());

       $this->assertCount(1,$jane->notifications);
   }

   /** @test */
   public function fetch_users_for_replying_to_post()
   {
       create('App\User', ['name' => 'johndoe']);
       create('App\User', ['name' => 'johndoe1']);

       $results = $this->json('GET', '/api/users', ['name' => 'john']);

       $this->assertCount(2, $results->json());
   }
}
