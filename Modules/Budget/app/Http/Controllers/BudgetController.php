<?php

namespace Modules\Budget\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('budget::index');
    }

    /**
     * Display années financières page.
     */
    public function anneesFinancieres()
    {
        return view('budget::annees-financieres');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('budget::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) 
    {
        // À implémenter si nécessaire
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('budget::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('budget::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) 
    {
        // À implémenter si nécessaire
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) 
    {
        // À implémenter si nécessaire
    }
}
