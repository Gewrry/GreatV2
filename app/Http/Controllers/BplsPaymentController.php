<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BplsPaymentController extends Controller
{
    public function index()
    {
        return view('modules.treasury.bpls-payment.index');
    }
}