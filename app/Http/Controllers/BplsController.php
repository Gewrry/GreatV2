<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BplsController extends Controller
{
    public function index()
    {
        return view('modules.bpls.index');
    }
}