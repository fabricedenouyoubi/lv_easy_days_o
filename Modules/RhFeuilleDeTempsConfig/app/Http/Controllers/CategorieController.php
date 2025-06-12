<?php

namespace Modules\RhFeuilleDeTempsConfig\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
     /**
     * Display a listing of categories.
     */
    public function index()
    {
        return view('rhfeuilledetempsconfig::index');
    }

    /**
     * Display années financières page.
     */
    public function categories()
    {
        return view('rhfeuilledetempsconfig::categories');
    }
    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('rhfeuilledetempsconfig::categories');
    }

    /**
     * Show the specified category.
     */
    public function show($id)
    {
        return view('rhfeuilledetempsconfig::categories', ['categorieId' => $id]);
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($id)
    {
        return view('rhfeuilledetempsconfig::categories', ['editingId' => $id]);
    }
}
