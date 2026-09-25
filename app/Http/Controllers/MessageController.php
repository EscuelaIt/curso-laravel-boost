<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    /**
     * Show the list of users that can receive a message.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('messages/Index', [
            'users' => User::query()
                ->whereKeyNot($request->user()->id)
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }
}
