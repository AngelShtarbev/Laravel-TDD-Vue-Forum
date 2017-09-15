<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function has_owner()
    {
        $reply = create('App\Reply');

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /** @test */
    public function reply_detects_if_it_was_published()
    {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    public function detect_all_mentioned_users_in_the_body()
    {
        $reply = new \App\Reply([
            'body' => '@JaneDoe wants to talk to @JohnDoe'
        ]);

        $this->assertEquals(['JaneDoe','JohnDoe'], $reply->mentionedUsers());
    }

    /** @test */
    public  function wraps_mentioned_usernames_in_the_body_within_anchor_tags()
    {
        $reply = new \App\Reply(['body' => 'Hello @Jane-Doe.']);

        $this->assertEquals('Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.', $reply->body);
    }
}
