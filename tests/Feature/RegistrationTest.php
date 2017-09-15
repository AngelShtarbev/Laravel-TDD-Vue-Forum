<?php
namespace Tests\Feature;

use App\Mail\EmailConfirmation;
use App\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test  */
    public function send_confirmation_email_upon_registration()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'testPass',
            'password_confirmation' => 'testPass'
        ]);

        Mail::assertSent(EmailConfirmation::class);
    }

    /** @test  */
    public function users_can_confirm_their_email_addresses()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'testPass',
            'password_confirmation' => 'testPass'
        ]);

        $user = User::whereName('John')->first();

        $this->assertFalse($user->confirmed);

        $this->assertNotNull($user->confirmation_token);

        $response = $this->get(route('register.confirm', ['token' => $user->confirmation_token]));

        $response->assertRedirect(route('threads'));

        $this->assertTrue($user->fresh()->confirmed);

        $this->assertNull($user->fresh()->confirmation_token);

    }

    /** @test  */
    public function confirming_an_invalid_token()
    {
        $response = $this->get(route('register.confirm', ['token' => 'invalid']));

        $response->assertRedirect(route('threads'));

        $response->assertSessionHas('flash', 'Unknown token.');
    }

}
