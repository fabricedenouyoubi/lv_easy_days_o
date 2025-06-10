<?php

namespace Modules\Rh\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PosteController extends Controller
{
   public function index()
   {
    return view('rh::poste-list');
   }
}