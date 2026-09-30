<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ErrorTestController extends Controller
{
    public function show(): View
    {
        return view('error-test');
    }
}
