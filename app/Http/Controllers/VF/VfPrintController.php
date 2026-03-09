<?php
// app/Http/Controllers/VF/VfPrintController.php

namespace App\Http\Controllers\VF;

use App\Http\Controllers\Controller;

class VfPrintController extends Controller
{
    public function permit($franchise)
    {
        // TODO: $franchise = Franchise::findOrFail($franchise);
        return view('modules.vf.prints.permit', compact('franchise'));
    }

    public function sticker($franchise)
    {
        // TODO: $franchise = Franchise::findOrFail($franchise);
        return view('modules.vf.prints.sticker', compact('franchise'));
    }

    public function orcr($franchise)
    {
        // TODO: $franchise = Franchise::findOrFail($franchise);
        return view('modules.vf.prints.orcr', compact('franchise'));
    }
}