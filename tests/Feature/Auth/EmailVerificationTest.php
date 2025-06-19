<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    //--- Vérifie que la page de vérification de l'adresse e-mail peut être affichée correctement.
    public function test_email_verification_screen_can_be_rendered(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $response = $this->actingAs($user)->get('/verify-email');

        $response
            ->assertSeeVolt('pages.auth.verify-email')
            ->assertStatus(200);
    }

    //--- Vérifie que l'adresse e-mail peut être vérifiée.
    public function test_email_can_be_verified(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    //--- Vérifie que l'adresse e-mail n'est pas vérifiée avec un hachage invalide.
    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
