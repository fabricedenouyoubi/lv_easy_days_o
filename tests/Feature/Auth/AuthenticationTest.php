<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    //--- Vérifie que la page de connexion peut être affichée correctement.
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    //--- Vérifie que les utilisateurs peuvent s'authentifier en utilisant l'écran de connexion.
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    //--- Vérifie que les utilisateurs ne peuvent pas s'authentifier avec un mot de passe invalide.
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest();
    }

    //--- Vérifie que la navigation du menu fonctionne correctement.
    public function test_navigation_menu_can_be_rendered(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response
            ->assertOk();
    }

    //--- Vérifie que les utilisateurs peuvent se déconnecter.
    public function test_users_can_logout(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $this->actingAs($user);

        $reponse = $this->post(route('logout'));

        $this->assertGuest();
    }
}
