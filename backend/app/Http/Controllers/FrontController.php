<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{

    public function panel()
    {
		return view('panel');
    }

    public function app()
    {
    	return view('app');
    }
}
