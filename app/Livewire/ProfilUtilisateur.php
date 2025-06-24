<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ProfilUtilisateur extends Component
{
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    //--- Règles de validation pour la modification du mot de passe d'un employe
    public function rules()
    {
        return [
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->mixedCase()
                    ->symbols()
            ],
            'new_password_confirmation' => 'required'
        ];
    }

    //--- Messages de validation pour la modification du mot de passe d'un employe
    public function messages()
    {
        return [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'new_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'new_password.min' => 'Le nouveau mot de passe doit contenir au moins :min caractères.',
            'new_password.letters' => 'Le nouveau mot de passe doit contenir au moins une lettre.',
            'new_password.numbers' => 'Le nouveau mot de passe doit contenir au moins un chiffre.',
            'new_password.mixed' => 'Le nouveau mot de passe doit contenir des majuscules et des minuscules.',
            'new_password.symbols' => 'Le nouveau mot de passe doit contenir au moins un caractère spécial.',
            'new_password_confirmation.required' => 'La confirmation du mot de passe est obligatoire.'
        ];
    }

    //---  fonction de modification du mot de passe
    public function changePassword()
    {
        $this->validate();

        $user = User::findOrFail(Auth::user()->id);
        if (!Hash::check($this->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('L\'ancien mot de passe est incorrect.'),
            ]);
        }
        $user->update([
            'password' => $this->new_password
        ]);

        session()->flash('success', 'Mot de passe mis à jour avec succès.');

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    public function render()
    {
        return view('livewire.profil-utilisateur');
    }
}
