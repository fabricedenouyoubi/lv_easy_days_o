<?php

namespace Tests\Feature\Auth;

use App\Livewire\ProfilUtilisateur;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    //-- Vérifie qu’un utilisateur peut mettre à jour son mot de passe avec succès.
    public function test_user_can_change_password_with_valid_data()
    {

        $user = User::factory()->create([
            'password' => Hash::make('CorrectPassword123!')
        ]);

        $this->actingAs($user);

        Livewire::test(ProfilUtilisateur::class)
            ->set('current_password', 'CorrectPassword123!')
            ->set('new_password', 'NewPassword456!')
            ->set('new_password_confirmation', 'NewPassword456!')
            ->call('changePassword');
        $this->assertTrue(Hash::check('NewPassword456!', $user->fresh()->password));
    }

    //-- Vérifie qu’un mot de passe incorrect est rejeté lors de la mise à jour.
    public function test_error_is_thrown_if_current_password_is_incorrect()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CorrectPassword123!')
        ]);

        $this->actingAs($user);

        Livewire::test(ProfilUtilisateur::class)
            ->set('current_password', 'WrongPassword!')
            ->set('new_password', 'NewPassword456!')
            ->set('new_password_confirmation', 'NewPassword456!')
            ->call('changePassword')
            ->assertHasErrors(['current_password']);
    }

    //-- Vérifie que les erreurs de validation sont déclenchées pour une entrée invalide.
    public function test_validation_errors_are_triggered_for_invalid_input()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(ProfilUtilisateur::class)
            ->set('current_password', '')
            ->set('new_password', 'short')
            ->set('new_password_confirmation', 'different')
            ->call('changePassword')
            ->assertHasErrors([
                'current_password' => 'required',
                'new_password' => 'confirmed',
            ]);
    }
}
