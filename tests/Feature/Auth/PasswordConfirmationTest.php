<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    //--- Vérifie que la page de confirmation du mot de passe peut être affichée correctement.
    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response
            ->assertSeeVolt('pages.auth.confirm-password')
            ->assertStatus(200);
    }

    //--- Vérifie que le mot de passe peut être confirmé.
    public function test_password_can_be_confirmed(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $this->actingAs($user);

        $component = Volt::test('pages.auth.confirm-password')
            ->set('password', 'password');

        $component->call('confirmPassword');

        $component
            ->assertRedirect('/dashboard')
            ->assertHasNoErrors();
    }

    //--- Vérifie que le mot de passe n'est pas confirmé avec un mot de passe invalide.
    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $this->actingAs($user);

        $component = Volt::test('pages.auth.confirm-password')
            ->set('password', 'wrong-password');

        $component->call('confirmPassword');

        $component
            ->assertNoRedirect()
            ->assertHasErrors('password');
    }
}
