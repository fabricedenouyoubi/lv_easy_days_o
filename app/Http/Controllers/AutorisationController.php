<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AutorisationController extends Controller
{
    public function permission()
    {
        return view('autorisation.permission.index');
    }

    public function gestion_utilisateur()
    {
        return view('autorisation.gestion-utilisateur.index');
    }
}
