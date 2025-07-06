<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login-page');
    }

    public function logout()
    {
        $user = Auth::user();

        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        Log::channel('daily')->info("L'utilisateur ".$user->name." vient de se déconnecter.", ['userEnail' => $user->email, 'UserId' => $user->id]);

        return redirect()->route('login');
    }

    public function index()
    {
        return view('dashboard');
    }

    public function profile()
    {
        return view('profile');
    }
}
