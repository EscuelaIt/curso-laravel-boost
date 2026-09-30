<?php

namespace App\Http\Controllers;

class FooController extends Controller
{
    public function __invoke()
    {
        throw new \Exception('Error en Foo Controller');
    }
}
