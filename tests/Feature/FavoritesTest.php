<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test  */
    public function guests_can_not_favorite()
    {
//        $this->expectException('Illuminate\Auth\AuthenticationException')
//             ->post('replies/1/favorites')->assertRedirect('/login');
    }

    /** @test  */
    public function authenticated_user_can_favorite_reply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->post('replies/' . $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);
    }

    /** @test  */
    public function authenticated_user_can_favorite_reply_once()
    {
        $this->signIn();

        $reply = create('App\Reply');

        try {
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        } catch (\Exception $e) {
            $this->fail('Can\'t insert twice');
        }

        $this->assertCount(1, $reply->favorites);
    }

}
