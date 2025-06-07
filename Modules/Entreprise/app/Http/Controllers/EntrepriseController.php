<?php

namespace Modules\Entreprise\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EntrepriseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('entreprise.presentation');
    }

    /**
     * Page de présentation de l'entreprise
     */
    public function presentation()
    {
        return view('entreprise::presentation');
    }

    /**
     * Page de gestion des sites
     */
    public function sites()
    {
        return view('entreprise::sites');
    }
}
