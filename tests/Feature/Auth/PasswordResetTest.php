<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    //---  Vérifie que la page de demande de lien de réinitialisation du mot de passe peut être affichée correctement.
    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response
            ->assertSeeVolt('pages.auth.forgot-password')
            ->assertStatus(200);
    }

    //---  Vérifie qu'un lien de réinitialisation du mot de passe peut être demandé.
    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = User::where('email', 'admin@mail.com')->first();

        Volt::test('pages.auth.forgot-password')
            ->set('email', $user->email)
            ->call('sendPasswordResetLink');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    //---  Vérifie que l'écran de réinitialisation du mot de passe peut être affiché correctement.
    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = User::where('email', 'admin@mail.com')->first();

        Volt::test('pages.auth.forgot-password')
            ->set('email', $user->email)
            ->call('sendPasswordResetLink');

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response
                ->assertSeeVolt('pages.auth.reset-password')
                ->assertStatus(200);

            return true;
        });
    }

    //---  Vérifie que le mot de passe peut être réinitialisé.
    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = User::where('email', 'admin@mail.com')->first();

        Volt::test('pages.auth.forgot-password')
            ->set('email', $user->email)
            ->call('sendPasswordResetLink');

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $component = Volt::test('pages.auth.reset-password', ['token' => $notification->token])
                ->set('email', $user->email)
                ->set('password', '#2Mon_mot_de_passe2#')
                ->set('password_confirmation', '#2Mon_mot_de_passe2#');

            $component->call('resetPassword');

            $component
                ->assertRedirect('/login')
                ->assertHasNoErrors();

            return true;
        });
    }
}
