<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    //-- Vérifie qu’un utilisateur peut mettre à jour son mot de passe avec succès.
    public function test_password_can_be_updated(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = User::where('email', 'admin@mail.com')->first();

        $this->actingAs($user);

        $component = Volt::test('profile.update-password-form')
            ->set('current_password', 'password')
            ->set('password', '#2Mon_mot_de_passe2#')
            ->set('password_confirmation', '#2Mon_mot_de_passe2#')
            ->call('updatePassword');

        $component
            ->assertHasNoErrors()
            ->assertNoRedirect();

        $this->assertTrue(Hash::check('#2Mon_mot_de_passe2#', $user->refresh()->password));
    }

   //-- Vérifie qu’un mot de passe incorrect est rejeté lors de la mise à jour.
    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = User::where('email', 'admin@mail.com')->first();

        $this->actingAs($user);

        $component = Volt::test('profile.update-password-form')
            ->set('current_password', 'wrong-password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword');

        $component
            ->assertHasErrors(['current_password'])
            ->assertNoRedirect();
    }
}
