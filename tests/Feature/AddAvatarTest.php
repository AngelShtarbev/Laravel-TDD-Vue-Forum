<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddAvatarTest extends TestCase
{
   use DatabaseMigrations;

   /** @test*/
   public function only_members_can_add_avatars()
   {
      $response = $this->json('POST', 'api/users/1/avatar');

      $response->assertStatus(401);
   }

   /** @test*/
   public function valid_avatar_must_be_provided()
   {
      $this->signIn();

      $response = $this->json('POST', 'api/users/'. auth()->id() . '/avatar', [
          'avatar' => 'not-an-image'
      ]);

      $response->assertStatus(422);
   }

   /** @test*/
   public function user_can_add_avatar_to_their_profile()
   {
       $this->signIn();

       Storage::fake('public');

       $file = UploadedFile::fake()->image('avatar.jpg');

       $this->json('POST', 'api/users/'. auth()->id() . '/avatar', [
           'avatar' => $file
       ]);

       $this->assertEquals(asset('avatars/' . $file->hashName()), auth()->user()->avatar_path);

       Storage::disk('public')->assertExists('avatars/' . $file->hashName());
   }

}
