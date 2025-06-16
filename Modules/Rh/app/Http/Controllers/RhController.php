<?php

namespace Modules\Rh\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function employe_list()
    {
        return view('rh::employe.index');
    }

    public function employe_details()
    {

    }
}
