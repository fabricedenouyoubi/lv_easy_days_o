<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AutorisationController extends Controller
{
    //--- Liste des permissions
    public function permission()
    {
        return view('autorisation.permission.index');
    }

    //--- liste des utilisateurs
    public function gestion_utilisateur()
    {
        return view('autorisation.gestion-utilisateur.index');
    }

    //--- liste des groupes
    public function groupe()
    {
        return view('autorisation.groupe.index');
    }
}
