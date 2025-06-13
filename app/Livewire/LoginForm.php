<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class LoginForm extends Component
{
    public $password;
    public $email;
    public $remember_me;


    public function mount()
    {
        $this->remember_me = false;
    }

    public function rules()
    {
        return [
            'password' => [
                'required',
                /* Password::min(8)
                    ->letters()
                    ->numbers()
                    ->mixedCase()
                    ->symbols() */
            ],
            'email' => 'required|email'
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            /* 'password.min' => 'Le nouveau mot de passe doit contenir au moins :min caractères.',
            'password.letters' => 'Le nouveau mot de passe doit contenir au moins une lettre.',
            'password.numbers' => 'Le nouveau mot de passe doit contenir au moins un chiffre.',
            'password.mixed' => 'Le nouveau mot de passe doit contenir des majuscules et des minuscules.',
            'password.symbols' => 'Le nouveau mot de passe doit contenir au moins un caractère spécial.', */
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'L’adresse e-mail doit être valide.',
        ];
    }

    //--- fontion de connexion d'un utilisateur
    public function login()
    {
        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember_me)) {
            session()->regenerate();
            $now_date = Carbon::now()->format('Y-m-d\TH:i');
            auth()->user()->update(['last_login' => $now_date]);
            return redirect()->intended(route('dashboard'));
        }

        session()->flash('error', 'Mot de passe ou adresse e-mail incorrecte.');
    }

    public function render()
    {
        return view('livewire.login-form');
    }
}
