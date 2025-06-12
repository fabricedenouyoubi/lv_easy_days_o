<?php

namespace Modules\RhFeuilleDeTempsConfig\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CodeTravailController extends Controller
{
     /**
     * Display a listing of codes de travail.
     */
    public function index()
    {
        return view('rhfeuilledetempsconfig::codes-travail');
    }
    public function codetravails()
    {
        return view('rhfeuilledetempsconfig::codes-travail');
    }

    /**
     * Show the form for creating a new code de travail.
     */
    public function create()
    {
        return view('rhfeuilledetempsconfig::codes-travail');
    }

    /**
     * Show the specified code de travail.
     */
    public function show($id)
    {
        return view('rhfeuilledetempsconfig::codes-travail', ['codeTravailId' => $id]);
    }

    /**
     * Show the form for editing the specified code de travail.
     */
    public function edit($id)
    {
        return view('rhfeuilledetempsconfig::codes-travail', ['editingId' => $id]);
    }

    /**
     * Show the configuration page for the specified code de travail.
     */
    public function configure($id)
    {
        return view('rhfeuilledetempsconfig::codes-travail', ['configuringId' => $id]);
    } 
}
